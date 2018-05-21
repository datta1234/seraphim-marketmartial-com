<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class TradeConfirmation extends Model
{
	/**
	 * @property integer $id
	 * @property integer $send_user_id
	 * @property integer $receiving_user_id
	 * @property integer $trade_id
	 * @property integer $trade_confirmation_statuse_id
	 * @property integer $trade_confirmation_id
	 * @property integer $stock_id
	 * @property integer $derivative_id
	 * @property integer $traiding_account_id
	 * @property double $spot_price
	 * @property double $future_reference
	 * @property double $near_expiery_reference
	 * @property double $contracts
	 * @property double $puts
	 * @property double $calls
	 * @property double $delta
	 * @property double $gross_premiums
	 * @property double $net_premiums
	 * @property boolean $is_confirmed
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trade_confirmations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spot_price',
		'future_reference',
		'near_expiery_reference',
		'contracts',
		'puts',
		'calls',
		'delta',
		'gross_premiums',
		'net_premiums',
		'is_confirmed',
    ];
}
