<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class MarketNegotiationStatus extends Model
{
	/**
	 * @property integer $id
	 * @property string $title
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'market_negotiation_statuses';
}
