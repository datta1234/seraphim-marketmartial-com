<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;
use App\Models\TradeConfirmations\TradeConfirmationItem;

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
        $trade_confirmation = $this->tradeConfirmation;
        $parent_group = null;
        
        // Resolved parent item groups only if has parent and the status is not 1 or 2
        if( !is_null($trade_confirmation->trade_confirmation_id) 
            && !in_array($trade_confirmation->trade_confirmation_status_id, [1,2,6,7]) ) {
            
            $parent_trade_confirmation = $trade_confirmation->resolveParent();
            $parent_group = TradeConfirmationGroup::where('trade_confirmation_id', $parent_trade_confirmation->id)
                ->where('trade_structure_group_id', $this->trade_structure_group_id)
                ->where('trade_structure_group_id', $this->trade_structure_group_id)
                ->first();
        }
        
        $parent_group_items = is_null($parent_group) ? null :  $parent_group->tradeConfirmationItems()
            ->where(function($query) use ($is_sender) {                
                $query
                ->whereNull('is_seller')
                ->orWhere('is_seller',$is_sender);
            })
            ->get();
        
        $item_ignore_list = ['is_offer'];

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
            ->map(function($item) use ($parent_group_items,$item_ignore_list) {
                if(is_null($parent_group_items)) {
                    return $item->preFormatted();
                } else {
                    return $item->preFormatted(in_array($item->title, $item_ignore_list) ? null 
                        : $parent_group_items->firstWhere('title', $item->title));
                }
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
