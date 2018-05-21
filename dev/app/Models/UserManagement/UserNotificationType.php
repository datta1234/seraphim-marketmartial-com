<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class UserNotificationType extends Model
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
    protected $table = 'user_notification_types';

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
    public function userNotifications()
    {
        return $this->hasMany('App\Models\UserManagement\UserNotification', 'user_notification_type_id');
    }
}
