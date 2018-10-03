<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class DistputeStatus extends Model
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
    protected $table = 'distpute_status';

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
    public function distputes()
    {
        return $this->hasMany('App\Models\TradeConfirmations\Distpute','distpute_status_id');
    }
}
