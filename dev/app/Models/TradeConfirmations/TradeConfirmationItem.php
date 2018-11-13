<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class TradeConfirmationItem extends Model
{
	/**
	 * @property integer $id
	 * @property integer $item_id
	 * @property integer $trade_confirmation_group_id
	 * @property string $value
	 * @property string $title
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "item_id",
        "title",
        "value",
        "trade_confirmation_group_id",
        "is_seller"
    ];


	/**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmationGroup()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\TradeConfirmationGroup',
        	'trade_confirmation_group_id');
    }

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function item()
    {
        return $this->belongsTo('App\Models\StructureItems\Item',
        	'item_id');
    }

    public function preFormatted()
    {
         return [
            'title' => $this->title,
            'value' => $this->value,
            'is_seller' => $this->is_seller

        ];
    }
}
