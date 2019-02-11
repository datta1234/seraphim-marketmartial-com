<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class BrokerageFee extends Model
{
    /**
	 * @property integer $id
	 * @property integer $organisation_id
	 * @property string $key
	 * @property double $value
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'brokerage_fees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organisation_id',
        'key',
        'value',
    ];

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function organisation()
    {
        return $this->belongsTo('App\Models\UserManagement\Organisation', 'organisation_id');
    }
}
