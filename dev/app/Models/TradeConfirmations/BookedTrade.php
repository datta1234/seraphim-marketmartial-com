<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class BookedTrade extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $trade_confirmation_id
	 * @property integer $traiding_account_id
	 * @property integer $derivative_id
	 * @property integer $stock_id
	 * @property integer $booked_trade_status_id
	 * @property boolean $is_sale
	 * @property boolean $is_confirmed
	 * @property double $amount
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'booked_trades';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_sale', 'is_confirmed', 'amount',
    ];
}
