<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $otp
	 * @property \Carbon\Carbon $expires_at
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_otps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
}
