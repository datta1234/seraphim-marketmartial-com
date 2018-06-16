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
        'title', 'email', 'notifiable','default_id'
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function defaultLabel()
    {
        return $this->belongsTo('App\Models\UserManagement\DefaultLabel', 'default_id');
    }

    public function getLabelAttribute()
    {
        return $this->defaultLabel()->exists() ? $this->defaultLabel->title : $this->title ; 
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function user()
    {
        return $this->belongsTo('App\Models\UserManagement\User', 'user_id');
    }
}
