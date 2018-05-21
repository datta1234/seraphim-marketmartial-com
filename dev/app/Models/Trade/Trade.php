<?php

namespace App\Models\Trade;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
	/**
	 * @property integer $id
	 * @property integer $trade_negotiation_id
	 * @property integer $user_market_id
	 * @property integer $initiate_user_id
	 * @property integer $recieving_user_id
	 * @property integer $trade_status_id
	 * @property double $contracts
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trades';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contracts'
    ];
}
