<?php

namespace App\Models\Trade;

use Illuminate\Database\Eloquent\Model;

class TradeNegotiationStatus extends Model
{
	/**
	 * @property integer $id
	 * @property string $name
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trade_negotiation_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
