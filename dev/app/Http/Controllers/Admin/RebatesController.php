<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Trade\Rebate;
use App\Models\UserManagement\User;
use App\Models\UserManagement\Organisation;
use App\Http\Requests\Admin\RebateUpdateRequest;
use Carbon\Carbon;

class RebatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rebates = Rebate::basicSearch(
                    $request->input('search'),
                    $request->input('_order_by') == '' ? null : $request->input('_order_by'),
                    $request->input('_order'),
                    [
                        "filter_paid" => $request->input('filter_paid') == '' ? null : $request->input('filter_paid'),
                        "filter_date" => $request->input('date_filter'),
                    ])
                ->paginate(10);
        $rebates->transform(function($rebate) {
            return $rebate->preFormatAdmin();
        });

        if($request->ajax()) {
            return $rebates;
        }
        return view('admin.rebates.index')->with(['rebates' => $rebates->toJson()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RebateUpdateRequest $request, Rebate $rebate)
    {
        if( $request->has('is_paid') ) {
            // deactivate and reactivate logic
            $rebate_update_result = $rebate->update([
                'is_paid' => $request->input('is_paid'),
            ]);

            if($rebate_update_result){
                return [
                    'success' => true,
                    'data' => $rebate->preFormatAdmin(),
                    'message' => 'Rebate Paid status successfully changed.'
                ];
            }
            return [
                'success' => false,
                'data' => null,
                'message' => 'Failed to change rebate Paid status.'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function downloadCsv(Request $request)
    {
        // Get the rebates according to the passed filters
        $rebates_query = Rebate::basicSearch(null,null,null, [
                    'filter_paid' => $request->input('is_paid'),
                    'filter_date' => $request->input('date'),
                    'filter_expiration' => $request->input('expiration')
                    ]);
        // Filter Rebates by organisation if passed
        if(!empty($request->input('organisation'))) {
            $rebates_query->whereHas('user',function($q) use ($request){
                $q->whereHas('organisation',function($q) use ($request){
                    $q->where('id',$request->input('organisation'));
                });
            });
        }

        $rebates = $rebates_query->get();

        $rebates->transform(function($rebate) {
            return $rebate->preFormatAdmin(true);
        });

        if($rebates->count() <= 0) {
            return redirect()->back()->with('error', 'The selected csv download has no records');
        }

        $filename = Carbon::now()->format('Y_m_d_His')."_MM_rebates.csv";
        $handle = fopen($filename, 'w+');

        $rebate_keys = array_keys($rebates->first());
        // Map CSV collumn headings to a config defined heading
        $csv_headings = array_map( function($value) {
                return config('marketmartial.export_csv_field_mapping.rebate_fields.'.$value);
            },$rebate_keys);
        fputcsv($handle, $csv_headings);

        foreach ($rebates as $rebate) {
            fputcsv($handle, $rebate);            
        }

        $headers = ['Content-Type' => 'text/csv'];
        return response()->download($filename, $filename, $headers)->deleteFileAfterSend(true);


    }
}
