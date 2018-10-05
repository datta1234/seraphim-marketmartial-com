<?php

namespace App\Models\StatsUploads;

use Illuminate\Database\Eloquent\Model;

class OpenInterest extends Model
{
    /**
	 * @property integer $id
	 * @property string $market_name
	 * @property string $contract
	 * @property \Carbon\Carbon $expiry_date
	 * @property boolean $is_put
	 * @property double $open_interest
	 * @property double $strike_price
	 * @property double $delta
	 * @property double $spot_price
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'open_interests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'market_name',
		'contract',
		'expiry_date',
		'is_put',
		'open_interest',
		'strike_price',
		'delta',
		'spot_price',
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

    /**
     * Creates a new OpenInterest record from the passed array
     *
     * @param array $data
     *
     * @return App\Models\StatsUploads\OpenInterest
     */
    public static function createFromCSV($data) {
    	
    	return self::create([
    		'market_name'	=> $data['market_name'],
			'contract'		=> $data['contract'],
			'expiry_date'	=> \Carbon\Carbon::parse($data['expiry_date']),
			'is_put'		=> ($data['is_put'] == 'P'),
			'open_interest'	=> self::doubleval($data['open_interest']),
			'strike_price'	=> self::doubleval($data['strike_price']),
			'delta'			=> self::doubleval($data['delta']),
			'spot_price'	=> self::doubleval($data['spot_price']),
    	]);
    }
}
