<?php
namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class UserMarketVolatility extends Model
{
    /**
     * @property integer $id
     * @property integer $user_market_id
     * @property integer $user_market_request_group_id
     * @property integer $volatility
     * @property \Carbon\Carbon $created_at
     * @property \Carbon\Carbon $updated_at
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_market_volatility';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_market_id',
        'user_market_request_group_id',
        'volatility',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $casts = [
        "id"    =>  "integer",
        "user_market_id"    =>  "integer",
        "user_market_request_group_id"  =>  "integer",
        "volatility"    =>  "double",
    ];


    public function preFormatted() {
        return [
            'id'        =>  $this->id,
            'group_id'  =>  $this->user_market_request_group_id,
            'value'     =>  $this->volatility,
        ];
    }
}
