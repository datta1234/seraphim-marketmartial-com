<?php

namespace App\Models\TradeConfirmations;

use Illuminate\Database\Eloquent\Model;

class SafexTradeConfirmation extends Model
{
    /**
	 * @property integer $id
	 * @property double $trade_id
	 * @property string $underlying
	 * @property \Carbon\Carbon $trade_date
	 * @property string $structure
	 * @property string $underlying_alt
	 * @property double $strike
	 * @property double $strike_percentage
	 * @property boolean $is_put
	 * @property string $volspread
	 * @property \Carbon\Carbon $expiry
	 * @property double $nominal
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'safex_trade_confirmations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trade_id',
		'underlying',
		'trade_date',
		'structure',
		'underlying_alt',
		'strike',
		'strike_percentage',
		'is_put',
		'volspread',
		'expiry',
		'nominal',
		'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public static function parseDouble($value) {
        return doubleval(str_replace(" ", "", $value));
    }
    public static function createFromCSV($data) {
    	
    	return self::create([
            'is_put' 			=> ($data['is_put'] == 'P'),
            'expiry' 			=> \Carbon\Carbon::parse($data['expiry']),
            'strike' 			=> self::parseDouble($data['strike']),
            'trade_id' 			=> self::parseDouble($data['trade_id']),
            'strike_percentage'	=> self::parseDouble($data['strike_percentage']),
            'nominal' 			=> self::parseDouble($data['nominal']),
			'underlying' 		=> $data['underlying'],
			'trade_date' 		=> \Carbon\Carbon::parse($data['trade_date']),
			'structure' 		=> $data['structure'],
			'underlying_alt' 	=> $data['underlying_alt'],
			'volspread' 		=> $data['volspread'],
			'created_at'		=> \Carbon\Carbon::now(),
			'updated_at'		=> \Carbon\Carbon::now(),
    	]);
    }
}
