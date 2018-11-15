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

        // Create a new array of csv file lines
        array_walk($csv, function(&$row) {
            array_walk($row, function(&$col) {
                $col = trim($col);
            });
        });

        // Replace the imported fields with the data base fields
        foreach ($csv[0] as $index => $field) {
            $csv[0][$index] = config('marketmartial.import_csv_field_mapping.open_interest_fields.'.$field);
        }

        // remove headings field and map each value to the heading as key value pair
        array_walk($csv, function(&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
            // removing white space before validation
            $a['open_interest'] = str_replace(" ", "", $a['open_interest']);
            $a['strike_price'] = str_replace(" ", "", $a['strike_price']);
            $a['delta'] = str_replace(" ", "", $a['delta']);
            $a['spot_price'] = str_replace(" ", "", $a['spot_price']);
        });
        array_shift($csv);

        // Validate the uploaded Csv fields
        $validator = Validator::make($csv,
            config('marketmartial.import_csv_field_mapping.open_interest_validation.rules'),
            config('marketmartial.import_csv_field_mapping.open_interest_validation.messages')
        );
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages(),
                'message' => 'Failed to upload Open Interest data.'
            ], 422);
        }

        // removes all previous open interest records
        OpenInterest::truncate();
        try {
            DB::beginTransaction();
            // create new records for each csv file entry
            $created = array_map('App\Models\StatsUploads\OpenInterest::createFromCSV', $csv);
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error($e);
            DB::rollBack();
            return response()->json(['message' => 'Failed to upload Open Interest data.', 'errors'=>[]], 500);
        }

        return response()->json(['data' => null,'message' => 'Open Interest data successfully uploaded.']);
    }
}
