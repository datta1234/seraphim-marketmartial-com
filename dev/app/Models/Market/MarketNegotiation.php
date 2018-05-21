<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class MarketNegotiation extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $market_negotiation_id
	 * @property integer $user_market_id
	 * @property double $bid
	 * @property double $offer
	 * @property double $bid_premium
	 * @property double $offer_premium
	 * @property double $future_reference
	 * @property double $spot_price
	 * @property boolean $has_premium_calc
	 * @property boolean $is_repeat
	 * @property boolean $is_accepted
	 * @property integer $market_negotiation_status_id
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'market_negotiations';
}
