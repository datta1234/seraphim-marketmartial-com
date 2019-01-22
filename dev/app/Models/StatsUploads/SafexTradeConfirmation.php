<?php

namespace App\Models\StatsUploads;

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

    /**
     * Creates a new OpenInterest record from the passed array
     *
     * @param array $data
     *
     * @return App\Models\StatsUploads\SafexTradeConfirmation
     */
    public static function createFromCSV($data) {
    	
    	return [
            'is_put' 			=> ($data['is_put'] == 'P'),
            'expiry' 			=> \Carbon\Carbon::parse($data['expiry']),
            'strike' 			=> doubleval($data['strike']),
            'trade_id' 			=> doubleval($data['trade_id']),
            'strike_percentage'	=> ( str_replace(" ", "",$data['strike_percentage']) == '-' ? 
                                        null : doubleval($data['strike_percentage'])
                                    ),
            'nominal' 			=> doubleval($data['nominal']),
			'underlying' 		=> $data['underlying'],
			'trade_date' 		=> \Carbon\Carbon::parse($data['trade_date']),
			'structure' 		=> $data['structure'],
			'underlying_alt' 	=> $data['underlying_alt'],
			'volspread' 		=> $data['volspread'],
			'created_at'		=> \Carbon\Carbon::now(),
			'updated_at'		=> \Carbon\Carbon::now(),
    	];
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
    public static function basicSearch($term = "",$orderBy="trade_id",$order='ASC', $filter = null)
    {
        if($orderBy == null)
        {
            $orderBy = "trade_id";
        }

        if($order == null)
        {
            $order = "DESC";
        }

        // Search markets
        $safex_trad_confirmation_query = SafexTradeConfirmation::where( function ($q) use ($term) {
            $q->where('underlying','like',"%$term%");
        });

        // Apply Filters
        if($filter !== null) {

            // Applies Date filter
            if(!empty($filter["filter_date"])) {
                $safex_trad_confirmation_query->whereDate('trade_date', $filter["filter_date"]);
            }

            // Applies Market filter
            if(!empty($filter["filter_market"])) {
                $market = $filter['filter_market'];
                switch ($market) {
                    case 'ALSI':
                    case 'DTOP':
                    case 'DCAP':
                        $safex_trad_confirmation_query->where( function ($q) use ($market) {
                            $q->where('underlying','like',"%$market%");
                        });
                        break;
                    case 'SINGLES':
                    default:
                        $safex_trad_confirmation_query->where( function ($q) {
                            $q->where('underlying','not like',"%ALSI%")
                                ->where('underlying','not like',"%DTOP%")
                                ->where('underlying','not like',"%DCAP%");
                        });
                        break;
                }
            }

            // Applies Expiration filter
            if(!empty($filter["filter_expiration"])) {
                $safex_trad_confirmation_query->whereDate('expiry', $filter["filter_expiration"]);
            }

            // Applies nominal filter
            if(!empty($filter["filter_nominal"])) {
                switch ($filter["filter_nominal"]) {
                    case '10-40':
                        $safex_trad_confirmation_query->whereBetween('nominal', [10000000,40000000]);
                        break;
                    case '>40':
                        $safex_trad_confirmation_query->where('nominal', ">" ,40000000);
                        break;
                }
            }
        }

        $safex_trad_confirmation_query->orderBy($orderBy,$order);

        return $safex_trad_confirmation_query;
    }
}
