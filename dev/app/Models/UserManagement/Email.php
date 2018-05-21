<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $default_id
	 * @property string $title
	 * @property string $email
	 * @property boolean $notifiable
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emails';
}
