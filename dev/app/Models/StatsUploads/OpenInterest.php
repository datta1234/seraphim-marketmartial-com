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
	 * @property string $spot_price
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
			'open_interest'	=> doubleval($data['open_interest']),
			'strike_price'	=> doubleval($data['strike_price']),
			'delta'			=> doubleval($data['delta']),
            // changed due to field type change [MM-811]
			//'spot_price'	=> doubleval($data['spot_price']),
            'spot_price'    => $data['spot_price'],
    	]);
    }

    /**
     * Return a simple or query object based on the search term
     *
     * @param string $term
     * @param string $orderBy
     * @param string $order
     * @param array  $filter
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function basicSearch($term = "",$orderBy="market_name",$order='ASC', $filter = null)
    {
        if($orderBy == null)
        {
            $orderBy = "market_name";
        }

        if($order == null)
        {
            $order = "ASC";
        }

        // Search term
        $open_interest_query = OpenInterest::where( function ($q) use ($term) {
            $q->where('market_name','like',"%$term%")
                ->orWhere('contract','like',"%$term%");
            // Search term where term is put or call
            if(strtolower($term) == 'put' || strtolower($term) == 'call') {
                $q->orWhere('is_put', (strtolower($term) == 'put' ? 1: 0));
            } 
        });

        // Apply Filters
        if($filter !== null) {

            // Applies Market filter
            if(!empty($filter["filter_market"])) {
                $market = $filter['filter_market'];
                switch ($market) {
                    case 'ALSI':
                        $open_interest_query->where( function ($q) use ($market) {
                            $q->where('contract','like',"%$market%")
                                ->orWhere('contract','like',"%ALSX%");
                        });
                        break;
                    case 'DTOP':
                        $open_interest_query->where( function ($q) use ($market) {
                            $q->where('contract','like',"%$market%")
                                ->orWhere('contract','like',"%DTOX%");
                        });
                        break;
                    case 'DCAP':
                        $open_interest_query->where( function ($q) use ($market) {
                            $q->where('contract','like',"%$market%")
                                ->orWhere('contract','like',"%DCAX%");
                        });
                        break;
                    case 'SINGLES':
                    default:
                        $open_interest_query->where( function ($q) {
                            $q->where('contract','not like',"%ALSI%")
                                ->where('contract','not like',"%ALSX%")
                                ->where('contract','not like',"%DTOP%")
                                ->where('contract','not like',"%DTOX%")
                                ->where('contract','not like',"%DCAP%")
                                ->where('contract','not like',"%DCAX%");
                        });
                        break;
                }
            }

            // Applies Expiration filter
            if(!empty($filter["filter_expiration"])) {
                $open_interest_query->whereDate('expiry_date', $filter["filter_expiration"]);
            }
        }

        $open_interest_query->orderBy($orderBy,$order);

        return $open_interest_query;
    }
}
