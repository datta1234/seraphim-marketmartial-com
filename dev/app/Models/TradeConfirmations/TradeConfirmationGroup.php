<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class TradeConfirmationGroup extends Model
{
	/**
	 * @property integer $id
	 * @property integer $trade_confirmation_id
	 * @property integer $trade_structure_group_id
	 * @property integer $user_market_request_group_id
	 * @property integer $trade_confirmation_group_id
	 * @property boolean $is_options
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "trade_structure_group_id",
        "trade_confirmation_id",
        "is_options",
        "user_market_request_group_id"
    ];

	/**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeStructureGroup()
    {
        return $this->belongsTo('App\Models\StructureItems\TradeStructureGroup',
        	'trade_structure_group_id');
    }

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmation()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\TradeConfirmation',
        	'trade_confirmation_id');
    }

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequestGroup()
    {
        return $this->belongsTo('App\Models\MarketRequest\UserMarketRequestGroup',
        	'user_market_request_group_id');
    }

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationItems()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationItem',
            'trade_confirmation_group_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationGroupParent()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\TradeConfirmationGroup',
        	'trade_confirmation_group_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationGroupChildren()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationGroup','trade_confirmation_group_id');
    }

    public function preFormatted($is_sender = null)
    {
        return [
            'id'                            => $this->id,
            'is_options'                    => $this->is_options,
            'user_market_request_group'     => $this->userMarketRequestGroup->preFormatted(),
            'trade_confirmation_items'      => 
            $this->tradeConfirmationItems()
            ->where(function($query) use ($is_sender) {                
                $query
                ->whereNull('is_seller')
                ->orWhere('is_seller',$is_sender);
            })
            ->get()
            ->map(function($item){
                return $item->preFormatted();
            })
        ];
    }

    public function setOpVal($title,$value,$is_sender = null)
    {
        $op = $this->tradeConfirmationItems->first(function($item) use ($title,$is_sender){
            $can_see = $item->is_seller === null || $item->is_seller == $is_sender;
            return strcasecmp($item->title,$title) == 0 && $can_see;
        });  
        

        if($op)
        {
            $op->value = $value;
            $op->save();
        }
    }

    public function getOpVal($title,$is_sender = null)
    {
        $marketRequestOptions =['Expiration Date','Expiration Date 1','Expiration Date 2','strike','Strike'];
        
        if(in_array($title, $marketRequestOptions))
        {
            $op = $this->userMarketRequestGroup->userMarketRequestItems->first(function($item) use ($title){
                return strcasecmp($item->title,$title) == 0;
            });
        }else
        {
            $op = $this->tradeConfirmationItems
            ->first(function($item) use ($title, $is_sender){
                $can_see = $item->is_seller === null || $item->is_seller == $is_sender;
                return strcasecmp($item->title,$title) == 0 && $can_see;
            });  
        }

        return  $op ? $op->value : null;
    }
}
