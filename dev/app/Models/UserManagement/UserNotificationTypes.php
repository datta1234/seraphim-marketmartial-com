<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class UserNotificationTypes extends Model
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
}
