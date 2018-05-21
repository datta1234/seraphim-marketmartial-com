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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'email', 'notifiable',
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function defaultLabels()
    {
        return $this->belongsTo('App\Models\UserManagement\DefaultLabel', 'default_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function users()
    {
        return $this->belongsTo('App\Models\UserManagement\User', 'user_id');
    }
}
