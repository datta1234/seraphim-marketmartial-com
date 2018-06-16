<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class InterestUser extends Model
{
	/**
	 * @property integer $id
	 * @property integer $interest_id
	 * @property integer $user_id
	 * @property string $value
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'interest_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function interest()
    {
        return $this->belongsTo('App\Models\UserManagement\Interest', 'interest_id');
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
