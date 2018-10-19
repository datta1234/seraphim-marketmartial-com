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
    public function tradeConfirmationGroupParents()
    {
        return $this->hasMany('App\Models\TradeConfirmations\TradeConfirmationGroup',
        	'trade_confirmation_group_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationGroupChildren()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\TradeConfirmationGroup','trade_confirmation_group_id');
    }

    public function preFormatted()
    {
        return [
            'is_options'                    => $this->is_options,
            'user_market_request_group'     => $this->userMarketRequestGroup->preFormatted(),
            'trade_confirmation_items'      => $this->tradeConfirmationItems->map(function($item){
                return $item->preFormatted();
            })
        ];
    }
}
