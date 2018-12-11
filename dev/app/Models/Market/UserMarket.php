<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\HasDismissibleActivity;
    
class UserMarket extends Model
{
    use \App\Traits\ResolvesUser,SoftDeletes;
    use HasDismissibleActivity; // activity tracked and dismissible

	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $user_market_request_id
	 * @property integer $user_market_status_id
	 * @property integer $current_market_negotiation_id
	 * @property boolean $is_trade_away
	 * @property boolean $is_market_maker_notified
     * @property boolean $is_hold_on
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 * @property \Carbon\Carbon $deleted_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_markets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'is_trade_away',
        'is_on_hold',
        'is_market_maker_notified',
        'user_market_request_id',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $casts = [
        'user_id'                   => 'int',
        'is_trade_away'             => 'Boolean',
        'is_on_hold'                => 'Boolean',
        'is_market_maker_notified'  => 'Boolean',
        'user_market_request_id'    => 'int',
    ];

    protected $hidden = ["user_id"];

    /**
    *   activityKey - identity for cached data
    */
    protected function activityKey() {
        return $this->id;
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiations()
    {
        return $this->hasMany('App\Models\Market\MarketNegotiation','user_market_id')
            ->when(config('loading_previous_day', false), function($q) {
                $q->previousDay();
            })
            ->orderBy('updated_at',"ASC");
    }

    public function scopeActiveQuotes($query, $active = true)
    {
        $query->where(function($q) use ($active) {
            // quote phase
            $q->whereHas('userMarketRequest', function($qq) use ($active) {
                $qq->whereNull('chosen_user_market_id');
            });
            // not killed
            $q->whereHas('firstNegotiation', function($qq) use ($active) {
                $qq->where('is_killed', '=', !$active);
            });
            
            // scoping by day -> current day or yesterday
            $q->when(!config('loading_previous_day', false), function($qq) {
                $qq->currentDay();
            });
            $q->when(config('loading_previous_day', false), function($qq) {
                $qq->previousDay();
            });
        });
    }

    /**
    *   Method to return if this quote is the best one that was available
    *   @return Boolean
    */
    public function isBestQuote()
    {
        /*
        * Handle VOL Spread - only when there DOESN'T exist a better(lower) VOL Spread
        */
        if($this->firstNegotiation->offer != null && $this->firstNegotiation->bid != null) {
            $vol = ($this->firstNegotiation->offer-$this->firstNegotiation->bid);
            $id = $this->id;
            return $this->userMarketRequest()
                ->whereHas('userMarkets', function($sub) use ($id, $vol) {
                    $sub->where('id', '!=', $id);
                    $sub->whereHas('marketNegotiations', function($q) use ($vol) {
                        $q->whereRaw('(offer-bid) < ?', [$vol]);
                    });
                })
                ->doesntExist();
        }
        /*
        * Handle BID/OFFER ONLY - only when there DOESN'T exist a better(bid:higher|offer:lower) bid/offer
        */
        $attr = $this->firstNegotiation->bid != null ? 'bid' : 'offer'; // has to be one of them
        $level = $this->firstNegotiation->{$attr};
        $id = $this->id;
        return $this->userMarketRequest()
            ->whereHas('userMarkets', function($sub) use ($id, $attr, $level) {
                $sub->where('id', '!=', $id);
                $sub->whereHas('firstNegotiation', function($q) use ($attr, $level) {
                    $symbol = ( $attr == 'bid' ? '>' : '<' );
                    $inverse = ( $attr == 'bid' ? 'offer' : 'bid' );
                    $q->where($attr, $symbol, $level);
                    $q->whereNull($inverse);
                });
            })
            ->doesntExist();
    }
    

    public function scopeCurrentDay($query) {
        return $query->whereBetween('updated_at', [ now()->startOfDay(), now()->addDays(1)->startOfDay() ]);
    }

    public function scopePreviousDay($query) {
        return $query->whereBetween('updated_at', [ now()->subDays(1)->startOfDay(), now()->startOfDay() ]);
    }

    public function scopeTraded($query)
    {
        return $query->whereHas('marketNegotiations', function($q){
            $q->whereHas('tradeNegotiations', function($qq){
                $qq->where('traded',true);  
            });
        });
    }

    public function scopeUntraded($query)
    {
        return $query->whereDoesntHave('marketNegotiations', function($q){
            $q->whereHas('tradeNegotiations', function($qq){
                $qq->where('traded',true);
            });
        });
    }

    public function marketNegotiationsDesc()
    {
        return $this->hasMany('App\Models\Market\MarketNegotiation','user_market_id')
            ->when(config('loading_previous_day', false), function($q) {
                $q->previousDay();
            })
            ->orderBy('updated_at',"DESC");
    }

    public function firstNegotiation()
    {
        return $this->hasOne('App\Models\Market\MarketNegotiation','user_market_id')->orderBy('created_at',"ASC")->orderBy('id',"ASC");
    }

    
    public function lastNegotiation()
    {
        return $this->hasOne('App\Models\Market\MarketNegotiation','user_market_id')
            ->when(config('loading_previous_day', false), function($q) {
                $q->previousDay();
            })
            ->orderBy('created_at',"DESC")
            ->orderBy('id',"DESC");
    }

    // conditions
    public function activeConditionNegotiations()
    {
        return $this->hasMany('App\Models\Market\MarketNegotiation','user_market_id')
                    ->when(config('loading_previous_day', false), function($q) {
                        $q->previousDay();
                    })
                    ->conditions()
                    ->whereDoesntHave('marketNegotiationChildren')
                    ->whereDoesntHave('tradeNegotiations')
                    ->orderBy('created_at',"DESC")
                    ->orderBy('id',"DESC");
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function currentMarketNegotiation()
    {
        return $this->belongsTo('App\Models\Market\MarketNegotiation','current_market_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function makerMarketNegotiation()
    {
        return $this->hasOne('App\Models\Market\MarketNegotiation','user_market_id')
                    ->where('is_killed', false)
                    ->orderBy('created_at', 'ASC')
                    ->orderBy('id', 'ASC');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketSubscriptions()
    {
        return $this->hasMany('App\Models\Market\UserMarketSubscription','user_market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequest()
    {
        return $this->belongsTo('App\Models\MarketRequest\UserMarketRequest','user_market_request_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function rebates()
    {
        return $this->hasMany('App\Models\Trade\Rebate','user_market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiations()
    {
        return $this->hasMany('App\Models\Trade\TradeNegotiation','user_market_id');
    }

    /**
    * Return related market volatilities
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function volatilities()
    {
        return $this->hasMany('App\Models\Market\UserMarketVolatility','user_market_id');
    }

    public function isTrading() {
        $trade = $this->tradeNegotiations()->latest()->first();
        return ( $trade ? !$trade->traded : false );
    }

    public function needsBalanceWorked()
    {
        $lastTrade = $this->tradeNegotiations()->latest()->first();
        if($lastTrade)
        {
             return $lastTrade->tradeNegotiationParent ? $lastTrade->quantity < $lastTrade->tradeNegotiationParent->quantity : false;          
        }else
        {
           return false;  
        }
        
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function user()
    {
        return $this->belongsTo('App\Models\UserManagement\User','user_id');
    }

    public function getOrganisationAttribute()
    {
       return $this->user->organisation;
    }

    /**
    * Return void
    * @params void
    */
    public static function placeOldQuotesOnHold()
    {
        //get all the user market that have no chosen market on market request the last 20 min
        $date = Carbon::now()->subMinutes(config('marketmartial.auto_on_hold_minutes',20));
        $updatedMarketsId = self::where('created_at','<=',$date)
        ->where('is_on_hold',false)
        ->whereDoesntHave('marketNegotiations',function($q){
            $q->where('is_accepted',true);
        })
        ->doesntHave('userMarketRequest.chosenUserMarket')
        ->pluck('id');
        
        if(count($updatedMarketsId) > 0)
        {
            self::whereIn('id',$updatedMarketsId)->update(['is_on_hold' => true]);
            self::whereIn('id',$updatedMarketsId)->each(function ($userMarket, $key) {
                $organisation = $userMarket->user->organisation;
                $userMarket->fresh()->userMarketRequest->notifyRequested([$organisation]);
            });  
        }
    }

    /**
    * Return Boolean
    * set the usermarket on hold, this will happen if its a quote
    * @params void
    */
    public function placeOnHold()
    {
        return $this->update(['is_on_hold'=>true]);
    }

    /**
    * Return Boolean
    * assocaite a marketrequest if its accepted by an interest
    * @params void
    */
    public function accept()
    {
        $marketRequest = $this->userMarketRequest;
        $marketRequest->chosenUserMarket()->associate($this);
        return $marketRequest->save();
    }

    /**
    * Return Boolean
    * if the person presented q qoute and was placed on hold the can repaet market negotaui
    * @params void
    */
    public function repeatQuote($user)
    {
        $marketNegotiation = $this->marketNegotiations()->where(function($query) use ($user) {
            $query->whereHas('user',function($query) use ($user) {
                $query->where('organisation_id', $user->organisation_id);
            });
        })->first();
        $this->update(['is_on_hold'=>false]);

      return  $marketNegotiation->update(['is_repeat'=>true]);    
    }

    /**
    * Return Boolean
    * if the person presented q qoute and was placed on hold the can repaet market negotaui
    * @params void
    */
    public function updateQuote($user,$data)
    {
        $marketNegotiation = $this->marketNegotiations()->where(function($query) use ($user)
        {
            $query->whereHas('user',function($query) use ($user){
                $query->where('organisation_id', $user->organisation_id);
            });
        })->first();
        $this->update(['is_on_hold'=>false]);

        if(isset($data['volatilities']) && !empty($data['volatilities'])) {
            $vols = collect($data['volatilities'])->keyBy('group_id');
            $groups = $this->volatilities;
            foreach($groups as $group) {
                if(isset($vols[$group->user_market_request_group_id])) {
                    $group->volatility = $vols[$group->user_market_request_group_id]['value'];
                    $done = $group->save();
                }
            }
        }

        return  $marketNegotiation->update($data);
    }


    private function setCounterOrgnisationAction($organisation_id)
    {
        $this->userMarketRequest->setAction(
            $organisation_id,
            $this->userMarketRequest->id,
            true
        );
    }

    public function findCounterNegotiation($user) {
        if($this->marketNegotiationsDesc()->count() == 1) {
            return $this->lastNegotiation;
        }
        return $this->marketNegotiationsDesc()
            ->findCounterNegotiation($user)
            ->first();
    }


    public function spinNegotiation($user)
    {
        // @TODO: this will fail with privates...
        $oldNegotiation = $this->lastNegotiation;       
        // $oldNegotiation = $userMarket->marketNegotiations()->orderBy('created_at', 'desc')->first();
        $marketNegotiation = $oldNegotiation->replicate();
        $marketNegotiation->is_repeat = true;

        // if its a repeat ATW just repeat it, dont repeat the condition
        if($marketNegotiation->isRepeatATW()) {
            $marketNegotiation->cond_is_repeat_atw = null;
        }

        $marketNegotiation->user_id = $user->id;
        $counterNegotiation = $this->findCounterNegotiation($user);

        if($counterNegotiation)
        {
            $marketNegotiation->market_negotiation_id = $counterNegotiation->id;
            $marketNegotiation->counter_user_id = $counterNegotiation->user_id;
        }

        try {
            DB::beginTransaction();
            $this->marketNegotiations()->save($marketNegotiation);
            $this->current_market_negotiation_id = $marketNegotiation->id;
            $this->save();

            if($counterNegotiation)
            {
                $this->setCounterOrgnisationAction($counterNegotiation->user->organisation_id);
            }
            DB::commit();
            return $marketNegotiation;
        } catch (\Exception $e) {
            \Log::error($e);
            DB::rollBack();
            return false;
        }
       
    }

    public function startNegotiationTree($marketNegotiation,$counterNegotiation,$user,$data)
    {
            //ensure that a new tree is started
            $marketNegotiation->market_negotiation_id = null;


            //quantity set to the default market request qunatity
            $qty = $this->userMarketRequest->getDynamicItem("quantity");

            if($marketNegotiation->bid != null && $marketNegotiation->offer != null) {
                // @TODO: well shit
                throw Exception("Can Only Improve One Side On New Negotian tree.");
            }
            elseif ($marketNegotiation->bid != null) {
                // improved bidw
                $source = $counterNegotiation->marketNegotiationSource('offer');
                $marketNegotiation->counter_user_id = $source->user_id;
                
                $marketNegotiation->offer = $source->offer;
                $marketNegotiation->offer_qty =  $qty;

            }
            elseif($marketNegotiation->offer != null) {
                // improved offer
                $source = $counterNegotiation->marketNegotiationSource('bid');
                $marketNegotiation->counter_user_id = $source->user_id;
                $marketNegotiation->bid = $source->bid;
                $marketNegotiation->bid_qty =  $qty ;
            }


        try {

            DB::beginTransaction();

            $this->marketNegotiations()->save($marketNegotiation);
            
            if($marketNegotiation->is_private) {
                $this->current_market_negotiation_id = $marketNegotiation->id;
            }
            
            $this->save();

            if($counterNegotiation) {
                 $this->setCounterOrgnisationAction($marketNegotiation->counterUser->organisation_id);
            }

            DB::commit();
            return $marketNegotiation;
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error($e);
            DB::rollBack();
            return false;
        }
    }

    public function addNegotiation($user,$data)
    {

        $marketNegotiation = new MarketNegotiation($data);
        $counterNegotiation = $this->findCounterNegotiation($user);


        $marketNegotiation->user_id = $user->id;

        if($counterNegotiation && $counterNegotiation->isTraded())
        {
            return $this->startNegotiationTree($marketNegotiation,$counterNegotiation,$user,$data);
        }


        // trade at best get improved
        //$this->userMarketRequest->getStatus($user->organisation_id) == "negotiation-open"
        if($this->userMarketRequest->isTradeAtBestOpen() && $this->userMarketRequest->getStatus($user->organisation_id) == "negotiation-open")
        {
            $counterNegotiation = $counterNegotiation->getImprovedNegotiation($marketNegotiation); 
        }


        if($counterNegotiation)
        {
            $marketNegotiation->market_negotiation_id = $counterNegotiation->id;

            //if parents are repeat
            if($counterNegotiation->is_repeat 
                && $counterNegotiation->marketNegotiationParent 
                && $counterNegotiation->marketNegotiationParent->is_repeat
            ) {
                if($marketNegotiation->bid != null && $marketNegotiation->offer != null) {
                    // @TODO: well shit
                    throw Exception("Can Only Improve One Side On SPIN");
                }
                elseif ($marketNegotiation->bid != null) {
                    // improved bid
                    $marketNegotiation->counter_user_id = $counterNegotiation->marketNegotiationSource('offer')->user_id;
                }
                elseif($marketNegotiation->offer != null) {
                    // improved offer
                    $marketNegotiation->counter_user_id = $counterNegotiation->marketNegotiationSource('bid')->user_id;
                }
            } else {
                $marketNegotiation->counter_user_id = $counterNegotiation->user_id;
            }

            // enforce responses to have the same condition
            if($counterNegotiation->isTradeAtBestOpen() && !$counterNegotiation->isTrading()) {
                $marketNegotiation->cond_buy_best = $counterNegotiation->cond_buy_best;
            }

            // responding to RepeatATW will open to market automatically - no longer happens
            if($counterNegotiation->isRepeatATW()) {
                $marketNegotiation->is_repeat = true;
            }

            // add missing values (prior data), however if traded keep them as null
            
                if($marketNegotiation->bid == null) {
                    $marketNegotiation->bid = $counterNegotiation->bid;
                    $marketNegotiation->bid_qty = $counterNegotiation->bid_qty;
                }
                if($marketNegotiation->offer == null) {
                    $marketNegotiation->offer = $counterNegotiation->offer;
                    $marketNegotiation->offer_qty = $counterNegotiation->offer_qty;   
                }  
        }
        // @TODO, this fails when you send new negotiation after you already have, need to stop this?
        try {
            DB::beginTransaction();

            $this->marketNegotiations()->save($marketNegotiation);
            if($marketNegotiation->is_private) {
                $this->current_market_negotiation_id = $marketNegotiation->id;
            }
            $this->save();
           
            if($counterNegotiation)
            {
                 $this->setCounterOrgnisationAction($counterNegotiation->user->organisation_id);
            }
            DB::commit();
            return $marketNegotiation;
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error($e);
            DB::rollBack();
            return false;
        }
    }

    public function updateNegotiation($marketNegotiation, $data)
    {
        if($data['bid'] == null) {
            $data['bid'] = $this->lastNegotiation->bid;
            $data['bid_qty'] = $this->lastNegotiation->bid_qty;
        }
        if($data['offer'] == null) {
            $data['offer'] = $this->lastNegotiation->offer;
            $data['offer_qty'] = $this->lastNegotiation->offer_qty;
        }
        $fields = [
            "bid",
            "offer",
            "offer_qty",
            "bid_qty",
            
            "bid_premium",
            "offer_premium",
            
            "has_premium_calc",
            // "is_repeat",
        ];
        // only let a condition be applied when one does not exist already
        if(!$marketNegotiation->hasCondition()) {
            $fields = array_merge($fields, [
                "cond_is_repeat_atw",
                "cond_fok_apply_bid",
                "cond_fok_spin",
                "cond_timeout",
                "cond_is_oco",
                "cond_is_subject",
                "cond_buy_mid",
                "cond_buy_best",
            ]);
        }
        $ret = $marketNegotiation->update(
            collect($data)->only($fields)->toArray()
        );
        if($marketNegotiation->counterUser)
        {
             $this->setCounterOrgnisationAction($marketNegotiation->counterUser->organisation_id);
        }
    }


    //when they need to work the balance
    public function workTheBalance($user,$quantity)
    {
        $lastMarketNegotiation = $this->lastNegotiation;
        $newMarketNegotiation = new MarketNegotiation();

        $newMarketNegotiation->user_market_id = $this->id;
        $newMarketNegotiation->market_negotiation_id = $lastMarketNegotiation->id;

        //see what the trade negotiation was started
        $lastTradeNegotiation = $lastMarketNegotiation->tradeNegotiations()->first();
        
       

        // $newMarketNegotiation->user_id = $user->id;

        /*
        * bid over → opens offer side
        * offered over → opens bid side
        */
        $attr = $lastTradeNegotiation->is_offer ? 'offer' : 'bid';
        $sourceNegotiation =  $lastMarketNegotiation->marketNegotiationSource($attr);
        // $newMarketNegotiation->user_id = $sourceNegotiation->user_id;
        $newMarketNegotiation->user_id = $user->id;

        if($lastTradeNegotiation->is_offer)
        {   
            $newMarketNegotiation->bid = $lastMarketNegotiation->offer;
            $newMarketNegotiation->bid_qty = $quantity;
            
        }else
        {
            $newMarketNegotiation->offer = $lastMarketNegotiation->bid;
            $newMarketNegotiation->offer_qty = $quantity;
        }

        $newMarketNegotiation->save();
        return $newMarketNegotiation;
    }


    public function isMaker($user = null) {
        $org = ($user == null ? $this->resolveOrganisationId() : $user->organisation_id);
        if($org == null) {
            return false;
        }
        if($this->makerMarketNegotiation == null) {
            return $org == $this->firstNegotiation->user->organisation_id;
        }
        return $org == $this->makerMarketNegotiation->user->organisation_id;
    }

    public function isInterest($user = null) {
        $org = ($user == null ? $this->resolveOrganisationId() : $user->organisation_id);
        if($org == null) {
            return false;
        }
        return $org == $this->userMarketRequest->user->organisation_id;
    }

    public function isCounter($user = null, $negotiation = null) {
        $org = ($user == null ? $this->resolveOrganisationId() : $user->organisation_id);
        if($org == null) {
            return false;
        }
        if($negotiation->marketNegotiationParent == null) {
            return null;
        }
        // deprecated logic, need to look at who is on the bid/offer as counter - use only as backup
        return $org == $negotiation->counterUser->organisation_id;
    }


    public function isSent($user = null, $negotiation = null) {
        $org = ($user == null ? $this->resolveOrganisationId() : $user->organisation_id);
        if($org == null) {
            return false;
        }
      
        return $org == $negotiation->user->organisation_id;
    }

    public function isWatched() {
        $organisation_id = $this->resolveOrganisationId();
        return $this->userMarketRequest->userSubscriptions()->where('organisation_id', $organisation_id)->exists();
    }

    public function isTradeAtBestOpen() {
        return  $this->lastNegotiation
            &&  $this->lastNegotiation->isTradeAtBestOpen()
            &&  !$this->lastNegotiation->isTrading();
    }
    

    /**
    * Return pre formatted request for frontend
    * @return \App\Models\Market\UserMarket
    */
    public function preFormattedMarket()
    {
        $is_maker = $this->isMaker();
        $is_interest = $this->isInterest();
        $is_watched = $this->isWatched();
        
        $uneditedmarketNegotiations = $marketNegotiations = $this->marketNegotiations()
            ->notPrivate($this->resolveOrganisationId())
            ->with('user')
            ->get();
        // @TODO addd back excludeFoKs but filter to only killed ones
        $creation_idx_map = [];


        $data = [
            "id"                    =>  $this->id,
            "is_interest"           =>  $is_interest,
            "is_maker"              =>  $is_maker,
            "is_watched"            =>  $is_watched,
            "time"                  =>  $this->created_at->format("H:i"),
            "market_negotiations"   =>  $marketNegotiations->map(function($item) use ($uneditedmarketNegotiations, &$creation_idx_map){
                                            $data = $item->setOrgContext($this->resolveOrganisation())
                                                        ->preFormattedMarketNegotiation($uneditedmarketNegotiations); 
                                            $org_id = $item->user->organisation_id;
                                            if(!in_array($org_id, $creation_idx_map)) {
                                                $creation_idx_map[] = $org_id;
                                            }
                                            $data['creation_idx'] = array_search($org_id, $creation_idx_map);
                                            return $data;
                                        }),
            "active_conditions"      => $this->activeConditionNegotiations->filter(function($cond){
                                            // only if counter
                                            $active = $this->isCounter(null, $cond);
                                            if($active === null) {
                                                $active = $this->isInterest(null);
                                            }
                                            return $active;
                                        })->map(function($cond) use ($uneditedmarketNegotiations) {
                                            return [
                                                'condition' => $cond->preFormattedMarketNegotiation($uneditedmarketNegotiations),
                                                'history'   => $cond->getConditionHistory()->map(function($item) use ($uneditedmarketNegotiations) {
                                                    return $item->setOrgContext($this->resolveOrganisation())
                                                                ->preFormattedMarketNegotiation($uneditedmarketNegotiations);
                                                })->values(),
                                                'type'      => $cond->activeConditionType
                                            ];
                                        })->values(),
            "sent_conditions"      => $this->activeConditionNegotiations->filter(function($cond){
                                            // only if counter
                                            $active = $this->isSent(null, $cond);
                                            return $active;
                                        })->map(function($cond) use ($uneditedmarketNegotiations) {
                                            return [
                                                'condition' => $cond->preFormattedMarketNegotiation($uneditedmarketNegotiations),
                                                'history'   => $cond->getConditionHistory()->map(function($item) use ($uneditedmarketNegotiations) {
                                                    return $item->setOrgContext($this->resolveOrganisation())
                                                                ->preFormattedMarketNegotiation($uneditedmarketNegotiations);
                                                })->values(),
                                                'type'      => $cond->activeConditionType
                                            ];
                                        })->values(),

            "volatilities"          =>  $this->volatilities->map(function($vol){
                                            return $vol->preFormatted();
                                        }),
        ];
        // dd($this->activeConditionNegotiations()->toSql());
        $data['activity'] = $this->getActivity('organisation.'.$this->resolveOrganisationId(), true);
        
        // if its trading at best
        $data['trading_at_best'] = (
            $this->isTradeAtBestOpen() ? 
            $this->lastNegotiation->tradeAtBestSource()->setOrgContext($this->resolveOrganisation())->preFormattedMarketNegotiation($uneditedmarketNegotiations) : 
            null 
        );

        // admin needs to see who owns what
        if($this->isAdminContext()) {
            $data['org'] = $this->user->organisation->title;
        }

        return $data;
    }

    /**
    * Return pre formatted request for frontend
    * @return \App\Models\Market\UserMarket
    */
    public function preFormattedPreviousDay()
    {

        $is_maker = $this->isMaker();
        $is_interest = $this->isInterest();
        $is_watched = $this->isWatched();
        
        $uneditedmarketNegotiations = $marketNegotiations = $this->marketNegotiations()
            // ->previousDay() // should be needed because of config('loading_previous_day', false) checks
            ->notPrivate($this->resolveOrganisationId())
            ->with('user')
            ->get();

        // @TODO addd back excludeFoKs but filter to only killed ones
        $creation_idx_map = [];


        $data = [
            "id"                    =>  $this->id,
            "is_interest"           =>  $is_interest,
            "is_maker"              =>  $is_maker,
            "is_watched"            =>  $is_watched,
            "time"                  =>  $this->created_at->format("H:i"),
            "market_negotiations"   =>  $marketNegotiations->map(function($item) use ($uneditedmarketNegotiations, &$creation_idx_map){
                                            $data = $item->setOrgContext($this->resolveOrganisation())
                                                        ->preFormattedMarketNegotiation($uneditedmarketNegotiations); 
                                            $org_id = $item->user->organisation_id;
                                            if(!in_array($org_id, $creation_idx_map)) {
                                                $creation_idx_map[] = $org_id;
                                            }
                                            $data['creation_idx'] = array_search($org_id, $creation_idx_map);
                                            return $data;
                                        }),
            "active_conditions"      => $this->activeConditionNegotiations->filter(function($cond){
                                            // only if counter
                                            $active = $this->isCounter(null, $cond);
                                            if($active === null) {
                                                $active = $this->isInterest(null);
                                            }
                                            return $active;
                                        })->map(function($cond) use ($uneditedmarketNegotiations) {
                                            return [
                                                'condition' => $cond->preFormattedMarketNegotiation($uneditedmarketNegotiations),
                                                'history'   => $cond->getConditionHistory()->map(function($item) use ($uneditedmarketNegotiations) {
                                                    return $item->setOrgContext($this->resolveOrganisation())
                                                                ->preFormattedMarketNegotiation($uneditedmarketNegotiations);
                                                })->values(),
                                                'type'      => $cond->activeConditionType
                                            ];
                                        })->values(),
            "sent_conditions"      => $this->activeConditionNegotiations->filter(function($cond){
                                            // only if counter
                                            $active = $this->isSent(null, $cond);
                                            return $active;
                                        })->map(function($cond) use ($uneditedmarketNegotiations) {
                                            return [
                                                'condition' => $cond->preFormattedMarketNegotiation($uneditedmarketNegotiations),
                                                'history'   => $cond->getConditionHistory()->map(function($item) use ($uneditedmarketNegotiations) {
                                                    return $item->setOrgContext($this->resolveOrganisation())
                                                                ->preFormattedMarketNegotiation($uneditedmarketNegotiations);
                                                })->values(),
                                                'type'      => $cond->activeConditionType
                                            ];
                                        })->values(),
            "volatilities"          =>  $this->volatilities->map(function($vol){
                                            return $vol->preFormatted();
                                        }),
        ];
        $data['activity'] = [];
        
        // if its trading at best
        $data['trading_at_best'] = (
            $this->isTradeAtBestOpen() ? 
            $this->lastNegotiation->tradeAtBestSource()->setOrgContext($this->resolveOrganisation())->preFormattedMarketNegotiation($uneditedmarketNegotiations) : 
            null 
        );

        // admin needs to see who owns what
        if($this->isAdminContext()) {
            $data['org'] = $this->user->organisation->title;
        }

        return $data;
    }

    /**
    * Return pre formatted request for frontend
    * @return \App\Models\Market\UserMarket
    */
    public function preFormattedQuote()
    {
        $is_maker = $this->isMaker();
        $is_interest = $this->isInterest();

        $data = [
            "id"            => $this->id,
            "is_interest"   =>  $is_interest,
            "is_maker"      => $is_maker,
            "bid_only"      => $this->currentMarketNegotiation->offer == null,
            "offer_only"    => $this->currentMarketNegotiation->bid == null,
            "vol_spread"    => (
                !$this->currentMarketNegotiation->offer == null && !$this->currentMarketNegotiation->bid == null ? 
                $this->currentMarketNegotiation->offer - $this->currentMarketNegotiation->bid : 
                null 
            ),
            "time"          => $this->created_at->format("H:i"),
        ];
        if($data['is_maker']) {
            $data['bid']        = $this->currentMarketNegotiation->bid;
            $data['offer']      = $this->currentMarketNegotiation->offer;
            $data['bid_qty']    = $this->currentMarketNegotiation->bid_qty;
            $data['offer_qty']  = $this->currentMarketNegotiation->offer_qty;
            $data['is_repeat']  = $this->currentMarketNegotiation->is_repeat;
            $data['is_on_hold'] = $this->is_on_hold;
        }
        if($data['is_interest']) {
            $data['is_on_hold'] = $this->is_on_hold;
        }

        // admin needs to see who owns what
        if($this->isAdminContext()) {
            $data['org'] = $this->user->organisation->title;
        }
        return $data;
    }

    /**
    * Return pre formatted request for frontend
    * @return \App\Models\Market\UserMarket
    */
    public function preFormatted()
    {

        $is_maker = $this->isMaker();
        $is_interest = $this->isInterest();

        $data = [
            "id"            => $this->id,
            "is_interest"   =>  $is_interest,
            "is_maker"      => $is_maker,
            "bid_only"      => $this->currentMarketNegotiation->offer == null,
            "offer_only"    => $this->currentMarketNegotiation->bid == null,
            "vol_spread"    => (
                !$this->currentMarketNegotiation->offer == null && !$this->currentMarketNegotiation->bid == null ? 
                $this->currentMarketNegotiation->offer - $this->currentMarketNegotiation->bid : 
                null 
            ),
            "time"          => $this->created_at->format("H:i"),
        ];
        if($data['is_maker']) {
            $data['bid']        = $this->currentMarketNegotiation->bid;
            $data['offer']      = $this->currentMarketNegotiation->offer;
            $data['bid_qty']    = $this->currentMarketNegotiation->bid_qty;
            $data['offer_qty']  = $this->currentMarketNegotiation->offer_qty;
        }

        // admin needs to see who owns what
        if($this->isAdminContext()) {
            $data['org'] = $this->user->organisation->title;
        }
        return $data;
    }



}
