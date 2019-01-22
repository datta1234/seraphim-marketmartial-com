<?php

namespace App\Http\Controllers\Stats;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stats\CsvUploadDataRequest;
use App\Models\StatsUploads\OpenInterest;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;

class OpenInterestControlller extends Controller
{   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $open_interests = OpenInterest::all();
        $grouped_open_interests = $open_interests->mapToGroups(function ($item, $key) {
            switch ($item['contract']) {
                case 'ALSI':
                case 'ALSX':
                    return ["ALSI" => $item];
                    break;
                case 'DTOP':
                case 'DTOX':
                    return ["DTOP" => $item];
                    break;
                case 'DCAP':
                case 'DCAX':
                    return ["DCAP" => $item];
                    break;
                default:
                    return ["SINGLES" => $item];
                    break;
            }
        });
        foreach ($grouped_open_interests as $market => $open_interests) {
            // group by contract
            $grouped_open_interests[$market] = $open_interests->groupBy(function ($item, $key) {
                return (string) $item['contract'];
            });

            foreach ($grouped_open_interests[$market] as $contract => $contract_group) {
                // group contracts by expiry date
                $grouped_open_interests[$market][$contract] = $contract_group->groupBy(function ($item, $key) {
                    return (string) $item['expiry_date'];
                })->sortBy(function ($item, $key) {
                    return $key;
                });

                foreach ($grouped_open_interests[$market][$contract] as $date => $date_group) {
                    // group expiry date by strike price
                    $grouped_open_interests[$market][$contract][$date] = $date_group->groupBy(function ($item, $key) {
                        return (string) $item['strike_price'];
                    });
                }
            }
        }

        return view('stats.open_interest')->with(compact('grouped_open_interests'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CsvUploadDataRequest $request)
    {
        $path = $request->file('csv_upload_file')->getRealPath();
        $csv = array_map('str_getcsv', file($path));
        
        if(count($csv) > 5001) {
            return response()->json([
                'errors' => [],
                'message' => 'Failed to upload Open Interest data. Csv file is larger than 5001 lines.'
            ], 422);
        }
        // 1. Create a new array of csv file lines
        array_walk($csv, function(&$row,$row_index) use (&$csv) {
            array_walk($row, function(&$col) use (&$csv,$row,$row_index) {
                if(count($row) <= 1 && $col == null) {
                    unset($csv[$row_index]);
                } else {
                    $col = trim($col);
                }
            });
        });

        // 2. Replace the imported fields with the data base fields
        foreach ($csv[0] as $index => $field) {
            $csv[0][$index] = config('marketmartial.import_csv_field_mapping.open_interest_fields.'.$field);
        }

        $errors = array();
        // 3. remove headings field and map each value to the heading as key value pair
        array_walk($csv, function(&$a, $idx) use ($csv,&$errors) {
            if(count($a) == count($csv[0])) {
                $a = array_combine($csv[0], $a);
                // 3.1 removing white space before validation
                $a['open_interest'] = str_replace(" ", "", $a['open_interest']);
                $a['strike_price'] = str_replace(" ", "", $a['strike_price']);
                $a['delta'] = str_replace(" ", "", $a['delta']);
                // removed due to field type change [MM-811]
                /*$a['spot_price'] = str_replace(" ", "", $a['spot_price']);*/

                if($idx > 0) {
                    // 3.2 Validate current row fields
                    $validator = Validator::make($a,
                        config('marketmartial.import_csv_field_mapping.open_interest_validation.rules'),
                        config('marketmartial.import_csv_field_mapping.open_interest_validation.messages')
                    );

                    if($validator->fails()) {
                        // 3.3 Add row validation errors to the list
                        $errors[$idx] = $validator->messages();
                    }
                }

            }
        });
        array_shift($csv);

        // 4. Return validation errors
        if (!empty($errors)) {
            return response()->json([
                'errors' => $errors,
                'message' => 'Failed to upload Open Interest data.'
            ], 422);
        }

        // 5. removes all previous open interest records
        OpenInterest::truncate();
        try {
            DB::beginTransaction();
            // 6. create new records for each csv file entry
            $created = array_map('App\Models\StatsUploads\OpenInterest::createFromCSV', $csv);
            OpenInterest::insert($created);
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error($e);
            DB::rollBack();
            return response()->json(['message' => 'Failed to upload Open Interest data.', 'errors'=>[]], 500);
        }

        return response()->json(['data' => null,'message' => 'Open Interest data successfully uploaded.']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function openInterestTableData(Request $request)
    {
        // @TODO - Change reqeust to a custom reqeust
        $data = OpenInterest::basicSearch(
            $request->input('search'),
            $request->input('_order_by'),
            $request->input('_order'),
            [
                "filter_expiration" => $request->input('filter_expiration'),
                "filter_market" => $request->input('filter_market'),
            ]
        )->paginate(25);

        return $data;
    }*/
}
