<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class UserMarket extends Model
{
	/**
	 * @property integer $id
	 * @property user_id $id
	 * @property user_market_request_id $id
	 * @property user_market_status_id $id
	 * @property current_market_negotiation_id $id
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
}
