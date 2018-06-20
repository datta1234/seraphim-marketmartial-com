<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class TradeConfirmationStatus extends Model
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
    protected $table = 'trade_confirmation_statuses';

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
    public function tradeConfirmations()
    {
        return $this->hasMany(
            'App\Models\TradeConfirmations\TradeConfirmation',
            'trade_confirmation_status_id'
        );
    }
}
