<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;
use App\Models\Trade\TradeNegotiation;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasDismissibleActivity;

class MarketNegotiation extends Model
{
    use \App\Traits\ResolvesUser, \App\Traits\AppliesConditions, SoftDeletes;
    use HasDismissibleActivity; // activity tracked and dismissible

	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $market_negotiation_id
	 * @property integer $user_market_id
	 * @property integer $market_negotiation_status_id
	 * @property double $bid
	 * @property double $offer
	 * @property double $bid_qty
     * @property double $offer_qty
     * @property double $bid_premium
     * @property double $offer_premium
     * @property double $future_reference
	 * @property boolean $has_premium_calc
	 * @property boolean $is_repeat
	 * @property boolean $is_accepted
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'market_negotiations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

            "user_id",
            "counter_user_id",
            "market_negotiation_id",
            "user_market_id",
            "bid",
            "offer",
            "offer_qty",
            "bid_qty",
            "bid_premium",
            "offer_premium",
            "future_reference",
            "has_premium_calc",
            "is_repeat",
            // "is_accepted",
            // "is_killed",

            "is_private",
            "cond_is_repeat_atw",
            "cond_fok_apply_bid",
            "cond_fok_spin",
            "cond_timeout",
            "cond_is_oco",
            "cond_is_subject",
            "cond_buy_mid",
            "cond_buy_best",
    ];

    protected $hidden = ["user_id","user"];


    protected $appends = ["time"];

    protected $casts = [
        'is_killed'             => 'Boolean',
        'is_private'            => 'Boolean',
        "cond_is_repeat_atw"    => 'Boolean',
        "cond_fok_apply_bid"    => 'Boolean',
        "cond_fok_spin"         => 'Boolean',
        "cond_timeout"          => 'Boolean',
        "cond_is_oco"           => 'Boolean',
        "cond_is_subject"       => 'Boolean',
        "cond_buy_mid"          => 'Boolean',
        "cond_buy_best"         => 'Boolean',
    ];

    protected $applicableConditions = [
        /*
            'attribute_name' => 'default_value'
        */
        'is_private' => false,
        "cond_is_repeat_atw" => null,
        "cond_fok_apply_bid" => null,
        "cond_fok_spin" => null,
        "cond_timeout" => null,
        "cond_is_oco" => null,
        "cond_is_subject" => null,
        "cond_buy_mid" => null,
        "cond_buy_best" => null,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    /**
    *   activityKey - identity for cached data
    */
    protected function activityKey() {
        return $this->id;
    }

    public static function boot()
    {
        parent::boot();
        static::saved(function ($model) {
            $user_market_request = $model->userMarket->userMarketRequest;
            if($user_market_request->openToMarket()) {
                $user_market_request->notifySubscribedUsers();
            }
        });
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiationParent()
    {
        return $this->belongsTo('App\Models\Market\MarketNegotiation','market_negotiation_id');
    }

    public function getConditionHistory()
    {
        $table = $this->table;
        $parentKey = $this->marketNegotiationParent()->getForeignKey();
        $id = (int)$this->id;
        $history = DB::select("
            SELECT *
                FROM (
                    SELECT @id AS _id, @private as _private, (
                        SELECT @id := $parentKey FROM $table WHERE id = _id
                    ) as parent_id, (
                        SELECT @private := is_private FROM $table WHERE id = _id
                    ) as parent_private
                    FROM (
                        SELECT @id := $id, @private := 1
                    ) tmp1
                    JOIN $table ON @id IS NOT NULL AND @private = 1
                ) parent_struct
                JOIN $table outcome ON parent_struct._id = outcome.id
        ");
        return self::hydrate($history)->sortBy('id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiationSource($attr)
    {
        $table = $this->table;
        $parentKey = $this->marketNegotiationParent()->getForeignKey();
        $id = (int)$this->id;
        $att = ($attr == 'bid' ? 'bid' : 'offer');
        $value = ($attr == 'bid' ? floatval($this->bid) : floatval($this->offer));
        $source = DB::select("
            SELECT *
                FROM (
                    SELECT @id AS _id, @attr as _attr, (
                        SELECT @id := $parentKey FROM $table WHERE id = _id
                    ) as parent_id, (
                        SELECT @attr := $att FROM $table WHERE id = _id
                    ) as parent_attr
                    FROM (
                        SELECT @id := $id, @attr := $value
                    ) tmp1
                    JOIN $table ON @id IS NOT NULL AND @attr = $value
                ) parent_struct
                JOIN $table outcome ON parent_struct._id = outcome.id
                ORDER BY _id ASC
                LIMIT 1
        ");
        return self::hydrate($source)->first();
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeAtBestSource()
    {
        $table = $this->table;
        $parentKey = $this->marketNegotiationParent()->getForeignKey();
        
        $id = (int)$this->id;
        $value = $this->cond_buy_best == true ? 1 : 0;

        $source = DB::select("
            SELECT *
                FROM (
                    SELECT @id AS _id, @cond as _cond, (
                        SELECT @id := $parentKey FROM $table WHERE id = _id
                    ) as parent_id, (
                        SELECT @cond := cond_buy_best FROM $table WHERE id = _id
                    ) as parent_cond
                    FROM (
                        SELECT @id := ?, @cond := ?
                    ) tmp1
                    JOIN $table ON @id IS NOT NULL AND @cond = ?
                ) parent_struct
                JOIN $table outcome ON parent_struct._id = outcome.id
                ORDER BY _id ASC
                LIMIT 1
        ",[
            $id,
            $value,
            $value,
        ]);

        return self::hydrate($source)->first();
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiationChildren()
    {
        return $this->hasMany('App\Models\Market\MarketNegotiation','market_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarket()
    {
        return $this->belongsTo('App\Models\Market\UserMarket','user_market_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function currentUserMarkets()
    {
        return $this->hasMany('App\Models\Market\UserMarket','current_market_negotiation_id');
    }


    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeNegotiations()
    {
        return $this->hasMany('App\Models\Trade\TradeNegotiation','market_negotiation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function lastTradeNegotiation()
    {
        return $this->hasOne('App\Models\Trade\TradeNegotiation','market_negotiation_id')->latest();
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function user()
    {
        return $this->belongsTo('App\Models\UserManagement\User','user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function counterUser()
    {
        return $this->belongsTo('App\Models\UserManagement\User','counter_user_id');
    }

    public function getTimeAttribute()
    {
        return $this->created_at->format("H:i");
    }

    public function getRatioAttribute() {
        return $this->getLatestBidQty() / $this->getLatestOfferQty();
    }

    public function getActiveConditionTypeAttribute()
    {
        // FoK (can also be private... needs to be first)
        if($this->cond_fok_apply_bid !== null || $this->cond_fok_spin !== null) {
            return 'fok';
        }
        // all private instances
        if($this->is_private == true) {
            // Meet In middle
            if($this->cond_buy_mid !== null) {
                return 'meet-in-middle';
            }
            // Meet At Best
            if($this->cond_buy_best !== null) {
                return 'trade-at-best';
            }
            // Proposal
            return 'proposal';
        }
        // Meet At Best - OPEN to market
        if($this->cond_buy_best !== null) {
            return 'trade-at-best-open';
        }
        // Repeat ATW
        if($this->cond_is_repeat_atw !== null) {
            return 'repeat-atw';
        }
        // OCO
        if($this->cond_is_oco !== null) {
            return 'oco';
        }
        // Subject
        if($this->cond_is_subject !== null) {
            return 'subject';
        }
        return null;
    }

    public function scopeFindCounterNegotiation($query,$user, $private = false)
    {
        return $query->where(function($q) use ($private, $user) {
            $q->where('is_private', $private);
            $q->whereHas('user',function($q) use ($user) {
                $q->where('organisation_id','!=',$user->organisation_id);
            });
        })->orderBy('created_at', 'DESC');
    }

    public function scopeConditions($query, $active = true)
    {
        return $query->where(function($q){
            foreach($this->applicableConditions as $key => $default) {
                if($key != 'cond_timeout') {
                    $q->orWhere($key, '!=', $default);
                }
            }
        })
        ->when($active, function($q) {
            // only active
            $q->where('is_killed', '=', false);
        })
        ->whereRaw('IF( `cond_timeout` IS NOT NULL, IF(`created_at` > ?, 1, 0), 1 ) = 1', [\Carbon\Carbon::now()->subMinutes(20)]);
    }

    /**
    * test if is RepeatATW
    * @return Boolean
    */
    public function isRepeatATW()
    {
        return $this->cond_is_repeat_atw !== null; 
    }

    /**
    * test if is RepeatATW
    * @return Boolean
    */
    public function isRepeatATWRepeat()
    {
        return $this->marketNegotiationParent &&  $this->marketNegotiationParent->cond_is_repeat_atw !== null; 
    }

    /**
    * test if is FoK
    * @return Boolean
    */
    public function isFoK()
    {
        return ($this->cond_fok_apply_bid !== null || $this->cond_fok_spin !== null); 
    }

    /**
    * test if is Proposal
    * @return Boolean
    */
    public function isProposal()
    {
        return (
            $this->is_private === true && 
            $this->cond_fok_spin === null &&
            $this->cond_fok_apply_bid === null &&
            $this->cond_buy_mid === null &&
            $this->cond_buy_best === null
        ); 
    }

    /**
    * test if is MeetInMiddle
    * @return Boolean
    */
    public function isMeetInMiddle()
    {
        return (
            $this->is_private == true && 
            $this->cond_buy_mid !== null
        ); 
    }

    /**
    * test if is MeetInMiddle
    * @return Boolean
    */
    public function isTradeAtBest()
    {
        return (
            $this->is_private == true &&
            $this->cond_buy_best !== null
        ); 
    }


    public function isTrading() {
        return $this->tradeNegotiations->count() > 0;
    }

    /**
    * test if is MeetInMiddle
    * @return Boolean
    */
    public function isTradeAtBestOpen()
    {
        return (
            $this->is_private == false &&
            $this->cond_buy_best !== null
        ); 
    }

    public function doTradeAtBest($user, $quantity, $is_offer) {
        // @TODO: rework to get latest
        // $tradeNegotiation = $this->addTradeNegotiation($user,[
        //     "quantity"  =>  $quantity,
        //     "is_offer"  =>  $is_offer
        // ]);

        // $this->fresh()->userMarket->userMarketRequest->notifyRequested();
    }


    public function getMessage($scenario) {
        switch($scenario) {
            case 'market_traded':
                $tradeNegotiation = $this->lastTradeNegotiation;
                $marketRequest = $this->userMarket->userMarketRequest;
                
                $buyer  =   $tradeNegotiation->is_offer 
                            ? $tradeNegotiation->recievingUser->organisation()->pluck('title')[0] 
                            : $tradeNegotiation->initiateUser->organisation()->pluck('title')[0];
                $seller =   $tradeNegotiation->is_offer 
                            ? $tradeNegotiation->initiateUser->organisation()->pluck('title')[0] 
                            : $tradeNegotiation->recievingUser->organisation()->pluck('title')[0];
                return "Bank ".$buyer." (buyer) and ".$seller." (seller) traded a ".$marketRequest->getSummary();

            break;
            case 'fok_timeout':
                $marketReq = $this->userMarket->userMarketRequest;
                $exp = (new \Carbon\Carbon($marketReq->getDynamicItem('Expiration Date')))->format("My");
                $message = $marketReq->market_title." ".$exp." ".$marketReq->getDynamicItem('Strike');
                $state = $this->is_killed ? "Timeout Occured" : "Timeout Started";

                return "FoK:".($this->cond_fok_spin ? 'Fill': 'Kill')." ".$state." for _".$this->user->full_name."_ on *".$message."*";
            break;
            case 'trade_at_best_timeout':
                $marketReq = $this->userMarket->userMarketRequest;
                
                $state = $this->isTrading() ? "Timeout Occured" : "Timeout Started";
                $term = $this->cond_buy_best ? 'Buy' : 'Sell';
                $level = $this->cond_buy_best ? $this->offer : $this->bid;
                
                $exp = (new \Carbon\Carbon($marketReq->getDynamicItem('Expiration Date')))->format("My");
                $message = $marketReq->market_title." ".$exp." ".$marketReq->getDynamicItem('Strike');
                if($this->isTrading()) {
                    $message .= " - Trading @ ".$level;
                }
                
                return $term." At Best: ".$state." for _".$this->user->full_name."_ on *".$message."*";
            break;
        }
    }

    public function kill($user = null)
    {
        $this->is_killed = true; // && with_fire = true ;)
        // if its a fill
        if($this->cond_fok_spin == true) {
            $this->is_repeat = true;
        }
        $this->save();

        // prefer fill
        if($this->cond_fok_spin == true) {
            $newNegotiation = $this->replicate();
            
            $newNegotiation->is_private = !$this->cond_fok_spin; // show or not
            $newNegotiation->market_negotiation_id = $this->id;

            $newNegotiation->user_id = $user ? $user->id : $this->counter_user_id; // for timeouts, the initiating counter user will be default

            $att = $this->cond_fok_apply_bid ? 'bid' : 'offer';
            // $inverse = $att == 'bid' ? 'offer' : 'bid';
            $sourceMarketNegotiation = $this->marketNegotiationSource($att);
            $newNegotiation->counter_user_id = $sourceMarketNegotiation->user_id;
            $newNegotiation->{$att} = null;


            $newNegotiation->cond_timeout = false; // dont apply on this one
            // Override the condition application
            $newNegotiation->fok_applied = true;
            return $newNegotiation->save();
        } else {
            // prefer kill
            // if this is the first one... send back to quote phase
            if($this->marketNegotiationParent == null) {
                $request = $this->userMarket->userMarketRequest;
                $request->chosen_user_market_id = null;
                $request->save();
            }
        }
    }

    public function counter($user, $data)
    {
        if($data['bid'] == null) {
            $data['bid'] = $this->bid;
        }
        if($data['offer'] == null) {
            $data['offer'] = $this->offer;
        }
        return $this->marketNegotiationChildren()->create([
            'user_id'       =>  $user->id,
            'counter_user_id'   =>  $this->user_id,
            'user_market_id'    =>  $this->user_market_id,
            'offer_qty'     =>  $this->offer_qty,
            'bid_qty'       =>  $this->bid_qty,

            'bid'           =>  $data['bid'],
            'offer'         =>  $data['offer'],
            'is_private'    =>  true,
        ]);
    }

    public function reject()
    {
        $userMarket = $this->userMarket;
        
        // delete history
        $history = $this->getConditionHistory();
        $history->each(function($item) {
            $item->delete();
        });
        
        // delete self
        $success = $this->delete();

        // reasign current if valid
        $current_market_negotiation_id = $userMarket->current_market_negotiation_id;
        if($success && ($current_market_negotiation_id == $this->id || $history->contains('id', $current_market_negotiation_id)) ) {
            $userMarket->currentMarketNegotiation()->associate($userMarket->lastNegotiation)->save();
        }
        return $success ? true : false;
    }

    public function repeat($user)
    {
        $newNegotiation = $this->replicate();
        // dont re-own a trade at best
        if(!$newNegotiation->isTradeAtBest()) {
            $newNegotiation->user_id = $user->id;
            $newNegotiation->counter_user_id = $this->user_id;
        }
        $newNegotiation->market_negotiation_id = $this->id;

        return $newNegotiation->save();
    }

    public function improveBest($user, $data)
    {
        $newNegotiation = $this->replicate();
        $newNegotiation->user_id = $user->id;
        $newNegotiation->counter_user_id = $this->user_id;
        $newNegotiation->market_negotiation_id = $this->id;

        // set bid/offer new values
        $att = $this->cond_buy_best == true ? 'offer' : 'bid';
        $newNegotiation->$att = $data[$att];
        return $newNegotiation->save();
    }

    public function resolvePrivateHistory()
    {
        // update history tree
        // $history = $this->getConditionHistory();
        // $history->each(function($item){
        //     $item->update([
        //         'is_private' => false
        //     ]);
        // });
        $this->update([
            "is_private" => false
        ]);
    }

    public function getLatestBid()
    {
        if($this->isTraded())
        {
            return null;
        }
        if($this->bid != null) {
            return $this->bid;
        }
        if($this->market_negotiation_id != null) {
            return $this->marketNegotiationParent->getLatestBid();
        }
        return null;
    }

    public function getLatestOffer()
    {
        if($this->isTraded())
        {
            return null;
        }
        if($this->offer != null) {
            return $this->offer;
        }
        if($this->market_negotiation_id != null) {
            return $this->marketNegotiationParent->getLatestOffer();
        }
        return null;
    }

    public function getLatestBidQty()
    {
        if($this->bid_qty != null) {
            return $this->bid_qty;
        }
        if($this->market_negotiation_id != null) {
            return $this->marketNegotiationParent->getLatestBidQty();
        }
        return null;
    }

    public function getLatestOfferQty()
    {
        if($this->offer_qty != null) {
            return $this->offer_qty;
        }
        if($this->market_negotiation_id != null) {
            return $this->marketNegotiationParent->getLatestOfferQty();
        }
        return null;
    }

    public function isTraded()
    {
        return $this->tradeNegotiations()->where(function($q){
            return $q->where('traded',true);  
        })->exists();
    }

    public function isSpun()
    {
        return $this->is_repeat && $this->marketNegotiationParent && $this->marketNegotiationParent->is_repeat;
    }

    public function getLatest($side)
    {
        $side = ( $side == 'bid' ? 'bid' : 'offer' );
        if($this->isTraded())
        {
            return null;
        }
        if($this->{$side} != null) {
            return $this->{$side};
        }
        if($this->market_negotiation_id != null) {
            return $this->marketNegotiationParent->getLatest($side);
        }
        return null;
    }


    public function repeatNegotiation($user)
    {
        $marketNegotiation = $this->replicate();
        $marketNegotiation->is_repeat = true;
        $marketNegotiation->user_id = $user->id;
        $marketNegotiation->market_negotiation_id = $this->id;
        $marketNegotiation->counter_user_id = $this->user_id;
        $marketNegotiation->save();

        /*
        *   Handle if this is a trade at best condition
        */
        if($this->isTradeAtBest()) {

        }
    }

    /**
    * Filter Scope on not FoKs
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopeExcludingFoKs($query)
    {
        return $query->where(function($q) {
            $q->where('cond_fok_apply_bid', null);
            $q->where('cond_fok_spin', null);
        });
    }

    /**
    * Filter Scope on not private
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopeNotPrivate($query, $organisation_id = null)
    {
        return $query->where(function($q) use ($organisation_id) {
            // show only the non private ones by default
            $q->where('is_private', false);

            // also show the private ones for certain situations (owned / traded)
            // $q->orWhere(function($qq) use ($organisation_id) {
            //     $qq->where('is_private', true);
            //     $qq->whereHas('user', function($qqq) use ($organisation_id) {
            //         $qqq->where('organisation_id', $organisation_id);
            //     });
            //     // $qq->where(function($qqq) use ($organisation_id) {
            //     //     // if it was created by the organisaiton viewing, we show it
            //     //     // OR If this has been traded, we show it
            //     //     $qqq->orWhereHas('tradeNegotiations'/*, function($qqqq) {
            //     //         $qqqq->where('traded', true);
            //     //     }*/);
            //     // });
            // });
        });
    }

    /*
    For markets that have been SPUN: 
        When I as a third party improve the BID, then the market goes into pending state between me and the person who was last on the OFFER. 
        If I improve the OFFER then it goes between me and the last party on the BID.
    */
    public function getImprovedNegotiation($market_negotiation)
    {
       if($this->bid != $market_negotiation->bid)
       {
            return $this;
       }
       else
       {
            // failing on first quote
            if($this->marketNegotiationParent == null)
            {
                return $this;
            }
            return $this->marketNegotiationParent;
       }
    }


    public function scopeOrganisationInvolved($query,$organisation_id)
    {
       return $query->whereHas('user',function($q) use ($organisation_id){
            $q->where('organisation_id',$organisation_id);
        });   
    }

    public function scopeOrganisationInvolvedOrCounters($query,$organisation_id)
    {
       return $query->whereHas('user',function($q) use ($organisation_id){
            $q->where('organisation_id',$organisation_id);
        })->orWhereHas('counterUser',function($q) use ($organisation_id){
            $q->where('organisation_id',$organisation_id);
        });   
    }


    public function scopeLastNegotiation($query)
    {
        return $query->latest()->limit(1);
    }

    public function getLastTradeNegotiationAttribute()
    {
        return $this->tradeNegotiations()->latest()->first();
    }


    public function setAmount($marketNegotiations,$attr)
    {
        // $source = $marketNegotiations->where($attr, $this->getAttribute($attr))->sortBy('id')->first();
        $source = $this->marketNegotiationSource($attr);
        if($this->is_killed && $this->getAttribute($attr) == null) {
            return "";
        }
        if($this->is_repeat && $this->id != $source->id)
        {
            if($this->user->organisation_id == $source->user->organisation_id 
                && !$this->isTradeAtBest() 
                && !$this->isTradeAtBestOpen() 
                && !$this->isRepeatATW()
                && !$this->isFoK()
            ) {
                return "SPIN";
            }
        }
        return $this->getAttribute($attr);
    }

    private function setCounterAction($counterNegotiation)
    {
         // Set action that needs to be taken for the org related to this marketNegotiation
        $this->userMarket->userMarketRequest->setAction(
            $counterNegotiation->recievingUser->organisation_id,
            $counterNegotiation->userMarket->userMarketRequest->id,
            true
        );
    }

    private function setMarketNegotiationAction()
    {
       $this->userMarket->userMarketRequest->setAction(
            $this->user->organisation_id,
            $this->userMarket->userMarketRequest->id,
            true
        ); 
    }

    public function addTradeNegotiation($user,$data)
    {
            $tradeNegotiation = new TradeNegotiation($data);
            $tradeNegotiation->initiate_user_id = $user->id;            
            $tradeNegotiation->user_market_id = $this->user_market_id;
            $counterNegotiation = null;   
            $newMarketNegotiation = null;

            if(count($this->tradeNegotiations) == 0)
            {
                 // find out who the the negotiation is sent to based of who set the level last
                $attr = $tradeNegotiation->is_offer ? 'offer' : 'bid';
                $sourceMarketNegotiation = $this->marketNegotiationSource($attr);
                $tradeNegotiation->recieving_user_id = $sourceMarketNegotiation->user_id;
                $tradeNegotiation->traded = false;
            }else
            {

                $counterNegotiation = $this->tradeNegotiations->last();


                
                $tradeNegotiation->trade_negotiation_id = $counterNegotiation->id;
                $tradeNegotiation->is_offer = !$counterNegotiation->is_offer; //swicth the type as it is counter so the opposite
                $tradeNegotiation->recieving_user_id = $counterNegotiation->initiate_user_id;

                //if it is greater it is an amend but if it is less or eqaul it is traded
                if($tradeNegotiation->quantity == $counterNegotiation->quantity)
                {
                    //create a new market negotiation if the quantity is 
                    $tradeNegotiation->traded = true;
                    
                    /*
                    * once a trade has happend no need for the new record
                    */
                    //$newMarketNegotiation =  $this->setMarketNegotiationAfterTrade($user);

                }else if ($tradeNegotiation->quantity < $counterNegotiation->quantity) 
                {
                    //work the balance first
                    $tradeNegotiation->traded = true;
                }else if ($tradeNegotiation->quantity > $counterNegotiation->quantity) 
                {
                    $tradeNegotiation->traded = false;
                }
            }            
 
            try {
                DB::beginTransaction();
                $this->tradeNegotiations()->save($tradeNegotiation);
                if($tradeNegotiation->traded) {
                    \Slack::postMessage([
                        "text"      => $this->getMessage('market_traded'),
                        "channel"   => env("SLACK_ADMIN_TRADES_CHANNEL")
                    ], 'trade');
                }
               
                if($newMarketNegotiation )
                {
                    $newMarketNegotiation->save();
                }

                /*
                * a trade has occured so generate the required trade confirmation
                */
                if($tradeNegotiation->traded)
                {
                   $tradeConfirmation =  $tradeNegotiation->setUpConfirmation();
                   $message = "Congrats on the trade! Complete the booking in the confirmation tab";
                   $organisation = $tradeConfirmation->sendUser->organisation;
                   $tradeConfirmation->notifyConfirmation($organisation,$message);
                }

                // if this was a private proposal, cascade public update to history 
                if($this->is_private == true) {
                    $this->resolvePrivateHistory();
                }

                DB::commit();

                if(!is_null($counterNegotiation))
                {
                    //alert the admin if trade size is less then the previous one
                    if($tradeNegotiation->quantity < $counterNegotiation->quantity)
                    {
                        //@TODO alert admin
                    }
                }

                if(!is_null($counterNegotiation))
                {
                    $this->setCounterAction($counterNegotiation);
                }
                else
                {
                    $this->setMarketNegotiationAction();
                }


                return $tradeNegotiation;
            } catch (\Exception $e) {
                \Log::error($e);
                DB::rollBack();
                return false;
            }
    }
    

   



    /**
    * Return pre formatted request for frontend
    * @return \App\Models\Market\UserMarket
    */
    public function preFormattedMarketNegotiation($uneditedmarketNegotiations = null)
    {
        if($uneditedmarketNegotiations == null) {
            $uneditedmarketNegotiations = $this->userMarket->marketNegotiations()
            ->notPrivate($this->resolveOrganisationId())
            ->with('user')
            ->get();
        }
        $currentUserOrganisationId = $this->user->organisation_id;
        $interestUserOrganisationId = $this->userMarket->userMarketRequest->user->organisation_id;
        $marketMakerUserOrganisationId = $this->userMarket->user->organisation_id;
        $loggedInUserOrganisationId = $this->resolveOrganisationId();

        $is_maker = is_null($marketMakerUserOrganisationId) ? false : $currentUserOrganisationId == $marketMakerUserOrganisationId;
        $is_interest = is_null($interestUserOrganisationId) ? false : $currentUserOrganisationId == $interestUserOrganisationId;

        $data = [
            'id'                    => $this->id,
            "market_negotiation_id" => $this->market_negotiation_id,
            "user_market_id"        => $this->user_market_id,
            "bid"                   => $this->bid,
            "offer"                 => $this->offer,
            // "bid_source"            => $bid_source,
            // "offer_source"          => $offer_source,
            "bid_display"           => $this->setAmount($uneditedmarketNegotiations,'bid'),
            "offer_display"         => $this->setAmount($uneditedmarketNegotiations,'offer'),
            "offer_qty"             => $this->offer_qty,
            "bid_qty"               => $this->bid_qty,
            "bid_premium"           => $this->bid_premium,
            "offer_premium"         => $this->offer_premium,
            "future_reference"      => $this->future_reference,
            "has_premium_calc"      => $this->has_premium_calc,
            "is_repeat"             => $this->is_repeat,
            "is_accepted"           => $this->is_accepted,
            "is_private"            => $this->is_private,
            "is_killed"             => $this->is_killed,
            "cond_is_repeat_atw"    => $this->cond_is_repeat_atw,
            "cond_fok_apply_bid"    => $this->cond_fok_apply_bid,
            // "cond_fok_spin"         => $this->cond_fok_spin,
            "cond_fok"              => $this->isFoK() ? true : null,
            "cond_timeout"          => $this->cond_timeout,
            "cond_is_oco"           => $this->cond_is_oco,
            "cond_is_subject"       => $this->cond_is_subject,
            "cond_buy_mid"          => $this->cond_buy_mid,
            "cond_buy_best"         => $this->cond_buy_best,
            "is_interest"           => $is_interest,
            "is_maker"              => $is_maker,
            "is_my_org"             => $currentUserOrganisationId == $loggedInUserOrganisationId,
            "time"                  => $this->time,
            "created_at"            => $this->created_at->toIso8601String(),
            "trade_negotiations"    => $this->tradeNegotiations->map(function($tradeNegotiation){
                
                return $tradeNegotiation->setOrgContext($this->resolveOrganisation())->preFormatted();
            })

        ];

        $data['activity'] = $this->getActivity('organisation.'.$this->resolveOrganisationId(), true);

        return $data;
    }

    public function getvolatilityAttribute()
    {
        return $this->tradeNegotiations()->first()->is_offer ? $this->offer : $this->bid;
    }

      public function getvolatilitySpredAttribute()
    {
        return $this->tradeNegotiations()->first()->is_offer ? $this->offer : $this->bid;
    }


    /* ============================== Conditions Start ============================== */

    /**
    * Apply is_provate condition
    */
    public function applyIsPrivateCondition() {
        // ... do nothing?
    }

    /**
    * Apply cond_is_repeat_atw condition
    */
    public function applyCondIsRepeatAtwCondition() {
        // set the repeat state on this negotiation
        $this->is_repeat = true;
    }

    /**
    * Alias cond_fok_apply_bid & cond_fok_spin
    */
    public function applyCondFokApplyBidCondition() { $this->applyFOKCondition(); }
    public function applyCondFokSpinCondition()     { $this->applyFOKCondition(); }
    /**
    * Apply cond_fok_apply_bid & cond_fok_spin
    */
    private $fok_applied = false;
    public function applyFOKCondition() {
        if (!$this->fok_applied) {
            $this->fok_applied = true;

            // Prefer to Spin (fill)
            if( $this->cond_fok_spin == true ) {
                // cond_fok_apply_bid
                
            }
            // Prefer To Kill
            else {
                // cond_fok_apply_bid
                $this->is_private = true;

                $this->cond_timeout = true;
                $this->applyCondTimeoutCondition(); // force it

                // notify admin
                $title_initiator = $this->user->organisation->title;
                \Slack::postMessage([
                    "text"      => $this->getMessage('fok_timeout'),
                    "channel"   => env("SLACK_ADMIN_NOTIFY_CHANNEL")
                ], 'timeout');
            }
        }
    }

    /**
    * Apply cond_timeout
    */
    private $timeout_cond_applied = false;
    public function applyCondTimeoutCondition() {
        if(!$this->timeout_cond_applied) {
            $this->timeout_cond_applied = true;
        }
    }
    public function applyCondTimeoutPostCondition() {
        if($this->timeout_cond_applied) {
            $job = new \App\Jobs\MarketNegotiationTimeout($this);
            dispatch($job->delay(config('marketmartial.thresholds.timeout', 1200)));
        }
    }

    /**
    * Apply cond_is_oco
    */
    public function applyCondIsOcdCondition() {

    }

    /**
    * Apply cond_is_subject
    */
    public function applyCondIsSubjectCondition() {

    }

    /**
    * Apply cond_buy_mid
    */
    public function applyCondBuyMidCondition() {
        // assumption it exists... it should since you cant apply this to a quote...
        $parent = $this->marketNegotiationParent;
        $bid = doubleval($parent->getLatestBid());
        $offer = doubleval($parent->getLatestOffer());

        // set to private
        $this->is_private = true;

        // if they are the same value.. weird right? but a thing
        if($bid === $offer) {
            $this->bid = $bid;
            $this->offer = $offer;
            return true;
        }
        
        $value = ($bid+$offer)/2;

        $this->bid = $value;
        $this->offer = $value;
        return true;
    }

    /**
    * Apply cond_buy_best
    */
    public function applyCondBuyBestCondition() {
        if($this->marketNegotiationParent && $this->marketNegotiationParent->cond_buy_best === null) {
            $this->is_private = true; // initial is private
            $this->is_repeat = true; // starts as a repeat
            
            $this->cond_timeout = true;
            $this->applyCondTimeoutCondition(); // force it

            $this->userMarket->userMarketRequest->notifyRequested();

            \Slack::postMessage([
                "text"      => $this->getMessage('trade_at_best_timeout'),
                "channel"   => env("SLACK_ADMIN_NOTIFY_CHANNEL")
            ], "timeout");

        } else {
            $this->is_private = false; // ensure it stays open if its the responses
        }
    }

    /* ============================== Conditions End ============================== */

}
