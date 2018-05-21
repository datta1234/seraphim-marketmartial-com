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
}
