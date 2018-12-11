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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function distputeStatuses()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\DistputeStatus','distpute_status_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeConfirmations()
    {
        return $this->belongsTo('App\Models\TradeConfirmations\TradeConfirmation','trade_confirmation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function sendUsers()
    {
        return $this->belongsTo('App\Models\UserManagement\User','send_user_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function receivingUsers()
    {
        return $this->belongsTo('App\Models\UserManagement\User','receiving_user_id');
    }

    /**
    * Get the activity type for context for this models events
    * @param string $context
    * @return string
    */
    public function getActivityType($context = "changed")
    {
        return "dispute";
    }
}
