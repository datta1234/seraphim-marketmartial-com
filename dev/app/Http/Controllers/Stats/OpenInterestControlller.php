<?php

namespace App\Http\Controllers\Stats;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stats\CsvUploadDataRequest;
use App\Models\StatsUploads\OpenInterest;
use Illuminate\Support\Facades\DB;

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
                    return ["DTOP" => $item];
                    break;
                case 'DCAP':
                    return ["DCAP" => $item];
                    break;
                default:
                    return ["SINGLES" => $item];
                    break;
            }
        });
        $grouped_open_interests['ALL'] = $open_interests;
        
        foreach ($grouped_open_interests as $key => $open_interests) {
            $grouped_open_interests[$key] = $open_interests->groupBy(function ($item, $key) {
                return (string) $item['strike_price'];
            })->sortBy(function ($item, $key) {
                return $key;
            });
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
        // @TODO - truncate table before new import and validation for CSV
        $path = $request->file('csv_upload_file')->getRealPath();
        $csv = array_map('str_getcsv', file($path));

        array_walk($csv, function(&$row) {
            array_walk($row, function(&$col) {
                $col = trim($col);
            });
        });

        foreach ($csv[0] as $index => $field) {
            $csv[0][$index] = config('marketmartial.import_csv_field_mapping.open_interest_fields.'.$field);
        }

        array_walk($csv, function(&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
        });
        array_shift($csv);
        
        try {
            DB::beginTransaction();
            $created = array_map('App\Models\StatsUploads\OpenInterest::createFromCSV', $csv);
            DB::commit();
        } catch (\Exception $e) {
            \Log::error($e);
            DB::rollBack();
            return ['success' => false,'data' => null, 'message' => 'Failed to upload Safex data.'];
        }

        return ['success' => true,'data' => null,'message' => 'Open Interest data successfully uploaded.'];
    }
}
