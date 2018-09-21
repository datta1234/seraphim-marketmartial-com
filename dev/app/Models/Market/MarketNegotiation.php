<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;
use App\Models\Trade\TradeNegotiation;
use Illuminate\Support\Facades\DB;


class MarketNegotiation extends Model
{
    use \App\Traits\ResolvesUser, \App\Traits\AppliesConditions;
    
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
            "counter_id",
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
            "is_accepted",
            "is_killed",

            "is_private",
            "cond_is_repeat_atw",
            "cond_fok_apply_bid",
            "cond_fok_spin",
            "cond_timeout",
            "cond_is_ocd",
            "cond_is_subject",
            "cond_buy_mid",
            "cond_buy_best",
    ];

    protected $hidden = ["user_id","user"];


    protected $appends = ["time"];

    protected $casts = [
        'is_private' => 'Boolean',
        "cond_is_repeat_atw" => 'Boolean',
        "cond_fok_apply_bid" => 'Boolean',
        "cond_fok_spin" => 'Boolean',
        "cond_timeout" => 'Boolean',
        "cond_is_ocd" => 'Boolean',
        "cond_is_subject" => 'Boolean',
        "cond_buy_mid" => 'Boolean',
        "cond_buy_best" => 'Boolean',
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
        "cond_is_ocd" => null,
        "cond_is_subject" => null,
        "cond_buy_mid" => null,
        "cond_buy_best" => null,
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketNegotiationParent()
    {
        return $this->belongsTo('App\Models\Market\MarketNegotiation','market_negotiation_id');
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
    public function user()
    {
        return $this->belongsTo('App\Models\UserManagement\User','user_id');
    }

    public function getTimeAttribute()
    {
        return $this->created_at->format("H:i");
    }

    public function scopeFindCounterNegotiation($query,$user)
    {
        return $query->whereHas('user',function($q) use ($user) {
            $q->where('id','!=',$user->id);
        })->orderBy('created_at', 'DESC');
    }

    /**
    * test if is FoK
    * @return Boolean
    */
    public function isFoK() {
        return ($this->cond_fok_apply_bid !== null || $this->cond_fok_spin !== null); 
    }

    public function kill() {
        $this->is_killed = true; // && with_fire = true; ;)
        $this->save();
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

    /*
    For markets that have been SPUN: When I as a third party improve the BID, then the market goes into pending state between me and the person who was last on the OFFER. If I improve the OFFER then it goes between me and the last party on the BID.
    */
    public function getImprovedNegotiation($market_negotiation)
    {
       if($this->bid != $market_negotiation->bid)
       {
            return $this;
       }else
       {
            return $this->marketNegotiationParent; 
       }
    }


    public function scopeOrganisationInvolved($query,$organisation_id)
    {
       return $query->whereHas('user',function($q) use ($organisation_id){
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
        $source = $marketNegotiations->where($attr, $this->getAttribute($attr))->sortBy('id')->first();
        if($this->is_repeat && $this->id != $source->id)
        {
            return $this->user->organisation_id == $source->user->organisation_id ? "SPIN" : $this->getAttribute($attr);
        }else
        {
            return $this->getAttribute($attr);
       
        }
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
            $tradeNegotiation->recieving_user_id = $this->user_id;
            $tradeNegotiation->user_market_id = $this->user_market_id;

            //set counter
            $counterNegotiation = null;

            if($counterNegotiation)
            {
                $tradeNegotiation->trade_negotiation_id = $counterNegotiation->id;
            }


            try {
                DB::beginTransaction();
                $this->tradeNegotiations()->save($tradeNegotiation);
                if($counterNegotiation)
                {
                    $this->setCounterAction($counterNegotiation);
                }else
                {
                    $this->setMarketNegotiationAction();
                }
                DB::commit();

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
    public function preFormattedQuote($uneditedmarketNegotiations)
    {

        $currentUserOrganisationId = $this->user->organisation_id;
        $interestUserOrganisationId = $this->userMarket->userMarketRequest->user->organisation_id;
        $marketMakerUserOrganisationId = $this->userMarket->user->organisation_id;
        $loggedInUserOrganisationId = $this->resolveOrganisationId();


        //dd($currentUserOrganisationId,$interestUserOrganisationId,$marketMakerUserOrganisationId,$loggedInUserOrganisationId);

         $is_maker = is_null($marketMakerUserOrganisationId) ? false : $currentUserOrganisationId == $marketMakerUserOrganisationId;
         $is_interest = is_null($interestUserOrganisationId) ? false : $currentUserOrganisationId == $interestUserOrganisationId;

        $data = [
            'id'                    => $this->id,
            "market_negotiation_id" => $this->market_negotiation_id,
            "user_market_id"        => $this->user_market_id,
            "bid"                   => $this->bid,
            "offer"                 => $this->offer,
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
            "cond_is_repeat_atw"    => $this->cond_is_repeat_atw,
            "cond_fok_apply_bid"    => $this->cond_fok_apply_bid,
            "cond_fok_spin"         => $this->cond_fok_spin,
            "cond_timeout"          => $this->cond_timeout,
            "cond_is_ocd"           => $this->cond_is_ocd,
            "cond_is_subject"       => $this->cond_is_subject,
            "cond_buy_mid"          => $this->cond_buy_mid,
            "cond_buy_best"         => $this->cond_buy_best,
            "is_interest"           => $is_interest,
            "is_maker"              => $is_maker,
            "is_my_org"             => $currentUserOrganisationId == $loggedInUserOrganisationId,
            "time"                  => $this->time,
            "created_at"            => $this->created_at->format("d-m-Y H:i:s"),
            "trade_negotiations"    => $this->tradeNegotiations->map(function($tradeNegotiation){
                return $tradeNegotiation->preFormatted();
            })

        ];

        return $data;
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
    private static $fok_applied = false;
    public function applyFOKCondition() {
        if (!self::$fok_applied) {
            self::$fok_applied = true;

            // Prefer to kill
            if( $this->cond_fok_spin == true ) {
                // cond_fok_apply_bid
                
            }
            // Prefer To Fill
            else {
                // cond_fok_apply_bid

            }
        }
    }

    /**
    * Apply cond_timeout
    */
    public function applyCondTimeoutCondition() {
        $job = new \App\Jobs\MarketNegotiationTimeout($this);
        dispatch($job->delay(config('marketmartial.thresholds.timeout', 1200)));
    }

    /**
    * Apply cond_is_ocd
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

    }

    /**
    * Apply cond_buy_best
    */
    public function applyCondBuyBestCondition() {

    }

    /* ============================== Conditions End ============================== */

}
