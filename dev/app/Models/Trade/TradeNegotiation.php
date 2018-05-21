<?php

namespace App\Models\Trade;

use Illuminate\Database\Eloquent\Model;

class TradeNegotiation extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_market_id
	 * @property integer $trade_negotiation_id
	 * @property integer $initiate_user_id
	 * @property integer $recieving_user_id
	 * @property integer $trade_negotiation_status_id
	 * @property double $contracts
	 * @property double $nominals
	 * @property boolean $is_offer
	 * @property boolean $is_distpute
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trade_negotiations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contracts', 'nominals', 'is_offer', 'is_distpute',
    ];
}
