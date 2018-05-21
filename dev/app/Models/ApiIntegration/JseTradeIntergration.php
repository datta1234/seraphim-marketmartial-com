<?php

namespace App\Models\ApiIntegration;

use Illuminate\Database\Eloquent\Model;

class JseTradeIntergration extends Model
{
	/**
	 * @property integer $id
	 * @property string $type
	 * @property string $title
	 * @property string $field
	 * @property text $value
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jse_trade_intergrations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'title', 'field','value',
    ];

    /**
     * Return relation based of _id_foreign index
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function bookedTrades()
    {
        return $this->belongsToMany('App\Models\TradeConfirmations\BookedTrade', 'booked_trade_jse_intergration', 'booked_trade_id', 'jse_intergration_id');
    }
}
