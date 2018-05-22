<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class SafexExpirationDate extends Model
{
	/**
	 * @property integer $id
	 * @property \Carbon\Carbon $expiration_date
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'safex_expiration_dates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'expiration_date',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public $dates = [
        'created_at',
        'updated_at',
        'expiration_date'
    ];
}
