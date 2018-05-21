<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $notifiable_id
	 * @property integer $user_notification_type_id
	 * @property text $data
	 * @property text $description
	 * @property string $type
	 * @property string $notifiable_type
	 * @property \Carbon\Carbon $read_at
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        
    ];
}
