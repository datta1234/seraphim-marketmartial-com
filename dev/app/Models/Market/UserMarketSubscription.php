<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class UserMarketSubscription extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $user_market_id
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 * @property \Carbon\Carbon $deleted_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_market_subscription';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
}
