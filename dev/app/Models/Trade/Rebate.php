<?php

namespace App\Models\Trade;

use Illuminate\Database\Eloquent\Model;

class Rebate extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $user_market_request_id
	 * @property integer $user_market_id
	 * @property integer $trade_id
	 * @property integer $organisation_id
	 * @property integer $booked_trade_id
	 * @property boolean $is_paid
	 * @property \Carbon\Carbon $trade_date
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rebates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_paid',
    ];
}
