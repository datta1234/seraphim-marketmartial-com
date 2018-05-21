<?php

namespace App\Models\MarketRequest;

use Illuminate\Database\Eloquent\Model;

class UserMarketRequestItem extends Model
{
    /**
     * @property integer $id
     * @property integer $item_id
     * @property integer $user_market_request_group_id
     * @property string $value
     * @property \Carbon\Carbon $created_at
     * @property \Carbon\Carbon $updated_at
     */
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_market_request_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
    ];
}
