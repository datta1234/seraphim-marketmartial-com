<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
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
    protected $table = 'interests';

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
    public function interestUsers()
    {
        return $this->hasMany('App\Models\UserManagement\InterestUser', 'interest_id');
    }
}
