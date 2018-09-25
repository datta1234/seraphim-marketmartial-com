<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserMarket extends Model
{
    use \App\Traits\ResolvesUser,SoftDeletes;
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
        'is_trade_away'             => 'boolean',
        'is_on_hold'                => 'boolean',
        'is_market_maker_notified'  => 'boolean',
        'user_market_request_id'    => 'int',
    ];

    protected $hidden = ["user_id"];



    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiations()
    {
        return $this->hasMany('App\Models\Market\MarketNegotiation','user_market_id');
    }

    public function firstNegotiation()
    {
        return $this->hasOne('App\Models\Market\MarketNegotiation','user_market_id')->orderBy('created_at',"ASC")->orderBy('id',"ASC");
    }

    public function lastNegotiation()
    {
        return $this->hasOne('App\Models\Market\MarketNegotiation','user_market_id')->orderBy('created_at',"DESC")->orderBy('id',"DESC");
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
        //get all the user market that have no chosen market on market request
        $date = Carbon::now()->subMinutes(config('marketmartial.auto_on_hold_minutes'))->toDateString();
        $updatedMarketsId = self::where('created_at','<=',$date)
        ->where('is_on_hold',false)
        ->whereDoesntHave('marketNegotiations',function($q){
            $q->where('is_accepted',true);
        })
        ->doesntHave('userMarketRequest.chosenUserMarket')
        ->select('id')
        ->get()
        ->pluck('id');

        if(count($updatedMarketsId) > 0)
        {
            self::whereIn('id',$updatedMarketsId)->update(['is_on_hold' => true]);
            self::whereIn('id',$updatedMarketsId)->each(function ($userMarket, $key) {
                $organisation = $userMarket->user->organisation;
                $userMarket->userMarketRequest->notifyRequested([$organisation]);
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
        $marketNegotiation = $this->marketNegotiations()->where(function($query) use ($user)
        {
            $query->whereHas('user',function($query) use ($user){
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
      
      return  $marketNegotiation->update($data);
    }


    private function setCounterAction($counterNegotiation)
    {
        $this->userMarket->userMarketRequest->setAction(
            $counterNegotiation->user->organisation_id,
            $this->userMarket->userMarketRequest->id,
            true
        );
    }


    public function spinNegotiation($user)
    {
            $oldNegotiation = $this->marketNegotiations()->orderBy('created_at', 'desc')->first();       
            // $oldNegotiation = $userMarket->marketNegotiations()->orderBy('created_at', 'desc')->first();
            $marketNegotiation = $oldNegotiation->replicate();
            $marketNegotiation->is_repeat = true;

            $marketNegotiation->user_id = $user->id;
            $counterNegotiation = $this->marketNegotiations()
                                            ->findCounterNegotiation($user)
                                            ->first();

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
                // if($counterNegotiation)
                // {
                //  $this->setCounterAction($counterNegotiation);
                // }
                DB::commit();
                return $marketNegotiation;
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
                DB::rollBack();
                return false;
            }
       
    }



    public function addNegotiation($user,$data)
    {
           

            $marketNegotiation = new MarketNegotiation($data);
            $marketNegotiation->user_id = $user->id;

            $counterNegotiation = $this->marketNegotiations()
                                            ->findcounterNegotiation($user)
                                            ->first();


            if($this->userMarketRequest->getStatus($user->organisation_id) == "negotiation-open")
            {
                $counterNegotiation = $counterNegotiation->getImprovedNegotiation($marketNegotiation); 
            }


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
                // if($counterNegotiation)
                // {
                //  $this->setCounterAction($counterNegotiation);
                // }
                DB::commit();
                return $marketNegotiation;
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
                DB::rollBack();
                return false;
            }
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

    public function isCounter($user = null) {
        $org = ($user == null ? $this->resolveOrganisationId() : $user->organisation_id);
        if($org == null) {
            return false;
        }
        if($this->currentMarketNegotiation->marketNegotiationParent == null) {
            return null;
        }
        return $org == $this->currentMarketNegotiation->marketNegotiationParent->user->organisation_id;
    }

    

    /**
    * Return pre formatted request for frontend
    * @return \App\Models\Market\UserMarket
    */
    public function preFormattedMarket()
    {
        $is_maker = $this->isMaker();
        $is_interest = $this->isInterest();
        
        $uneditedmarketNegotiations = $marketNegotiations = $this->marketNegotiations()->with('user')->get();
        // @TODO addd back excludeFoKs but filter to only killed ones

        $data = [
            "id"                    =>  $this->id,
            "is_interest"           =>  $is_interest,
            "is_maker"              =>  $is_maker,
            "time"                  =>  $this->created_at->format("H:i"),
            "market_negotiations"   =>  $marketNegotiations->map(function($item) use ($uneditedmarketNegotiations){
                                        return $item->setOrgContext($this->org_context)->preFormattedQuote($uneditedmarketNegotiations); 
                                        })
        ];

        // add Active FoK if exists
        if($this->currentMarketNegotiation->isFoK() && $this->currentMarketNegotiation->is_killed !== true) {
            // only if counter
            $active = $this->isCounter();
            if($active === null) {
                $active = $this->isInterest();
            }
            if($active) {
                $data['active_fok'] = $this->currentMarketNegotiation->preFormattedQuote($uneditedmarketNegotiations);
            }
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
            "id"    => $this->id,
            "is_interest"  =>  $is_interest,
            "is_maker"   => $is_maker,
            "bid_only" => $this->currentMarketNegotiation->offer == null,
            "offer_only" => $this->currentMarketNegotiation->bid == null,
            "vol_spread" => (
                !$this->currentMarketNegotiation->offer == null && !$this->currentMarketNegotiation->bid == null ? 
                $this->currentMarketNegotiation->offer - $this->currentMarketNegotiation->bid : 
                null 
            ),
            "time" => $this->created_at->format("H:i"),
        ];
        if($data['is_maker']) {
            $data['bid'] = $this->currentMarketNegotiation->bid;
            $data['offer'] = $this->currentMarketNegotiation->offer;
            $data['bid_qty'] = $this->currentMarketNegotiation->bid_qty;
            $data['offer_qty'] = $this->currentMarketNegotiation->offer_qty;
            $data['is_repeat'] = $this->currentMarketNegotiation->is_repeat;
            $data['is_on_hold'] = $this->is_on_hold;
        }
        if($data['is_interest']) {
            $data['is_on_hold'] = $this->is_on_hold;
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
            "id"    => $this->id,
            "is_interest"  =>  $is_interest,
            "is_maker"   => $is_maker,
            "bid_only" => $this->currentMarketNegotiation->offer == null,
            "offer_only" => $this->currentMarketNegotiation->bid == null,
            "vol_spread" => (
                !$this->currentMarketNegotiation->offer == null && !$this->currentMarketNegotiation->bid == null ? 
                $this->currentMarketNegotiation->offer - $this->currentMarketNegotiation->bid : 
                null 
            ),
            "time" => $this->created_at->format("H:i"),
        ];
        if($data['is_maker']) {
            $data['bid'] = $this->currentMarketNegotiation->bid;
            $data['offer'] = $this->currentMarketNegotiation->offer;
            $data['bid_qty'] = $this->currentMarketNegotiation->bid_qty;
            $data['offer_qty'] = $this->currentMarketNegotiation->offer_qty;
        }
        return $data;
    }

}
