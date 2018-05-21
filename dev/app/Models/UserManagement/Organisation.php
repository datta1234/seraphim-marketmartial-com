<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
	/**
	 * @property integer $id
	 * @property string $title
	 * @property boolean $verified
	 * @property text $description
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organisations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'verified', 'description',
    ];
}
