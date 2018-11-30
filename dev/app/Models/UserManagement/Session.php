<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    /**
     * @property integer $id
     * @property integer $user_id
     * @property string $ip_address
     * @property text $user_agent
     * @property text $payload
     * @property integer $last_activity
     */

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function user()
    {
        return $this->belongsTo('App\Models\UserManagement\User', 'user_id');
    }
}
