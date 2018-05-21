<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * @property integer $id
     * @property integer $role_id
     * @property integer $organisation_id
     * @property string $email
     * @property string $full_name
     * @property string $cell_phone
     * @property string $work_phone
     * @property string $password
     * @property string $remember_token
     * @property boolean $active
     * @property boolean $tc_accepted
     * @property boolean $is_married
     * @property boolean $has_children
     * @property text $hobbies
     * @property \Carbon\Carbon $last_login
     * @property \Carbon\Carbon $birthdate
     * @property \Carbon\Carbon $created_at
     * @property \Carbon\Carbon $updated_at
     * @property \Carbon\Carbon $deleted_at
     */

    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
