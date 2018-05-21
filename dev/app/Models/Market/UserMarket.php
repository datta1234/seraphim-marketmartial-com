<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class UserMarket extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $user_market_request_id
	 * @property integer $user_market_status_id
	 * @property integer $current_market_negotiation_id
	 * @property boolean $is_trade_away
	 * @property boolean $is_market_maker_notified
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
        'is_trade_away',
        'is_market_maker_notified',
    ];
}
