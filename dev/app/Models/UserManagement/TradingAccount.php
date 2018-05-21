<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class TradingAccount extends Model
{
	/**
	 * @property integer $id
	 * @property integer $user_id
	 * @property integer $derivative_id
	 * @property string $safex_number
	 * @property string $sub_account
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trading_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'safex_number', 'sub_account',
    ];
}
