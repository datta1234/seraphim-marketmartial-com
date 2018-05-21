<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class Distpute extends Model
{
	/**
	 * @property integer $id
	 * @property integer $send_user_id
	 * @property integer $receiving_user_id
	 * @property integer $distpute_status_id
	 * @property integer $trade_confirmation_id
	 * @property string $title
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'distputes';
}
