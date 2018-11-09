<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TradeConfirmations\BookedTrade;
use App\Http\Requests\Admin\BookedTradeUpdateRequest;
use Carbon\Carbon;

class BookedTradesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $booked_trades = BookedTrade::basicSearch(
                    $request->input('search'),
                    $request->input('_order_by') == '' ? null : $request->input('_order_by'),
                    $request->input('_order'),
                    [
                        "filter_status" => $request->input('filter_status') == '' ? null : $request->input('filter_status'),
                        "filter_date" => $request->input('date_filter'),
                    ])
                ->where('is_rebate', false)
                ->paginate(10);

        $booked_trades->transform(function($booked_trade) {
            return $booked_trade->preFormatAdmin();
        });

        if($request->ajax()) {
            return $booked_trades;
        }

        return view('admin.booked_trades.index')->with(['booked_trades' => $booked_trades->toJson()]);
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
    public function update(BookedTradeUpdateRequest $request, BookedTrade $booked_trade)
    {
        if( $request->has('is_confirmed') ) {
            // deactivate and reactivate logic
            $booked_trade_update_result = $booked_trade->update([
                'is_confirmed' => $request->input('is_confirmed'),
            ]);

            if($booked_trade_update_result){
                return response()->json([
                    'data' => $booked_trade->preFormatAdmin(),
                    'message' => 'Booked Trade status successfully changed.'
                ]);
            }
            return response()->json([
                'data' => null,
                'message' => 'Failed to change Booked Trade status.'
            ],500);
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
        // Get the booked_trades according to the passed filters
        $booked_trades_query = BookedTrade::basicSearch(null,null,null, [
                    'filter_status' => $request->input('is_confirmed'),
                    'filter_date' => $request->input('date'),
                    'filter_expiration' => $request->input('expiration')
                    ]);
        // Filter booked_trades by organisation if passed
        if(!empty($request->input('organisation'))) {
            $booked_trades_query->whereHas('user',function($q) use ($request){
                $q->whereHas('organisation',function($q) use ($request){
                    $q->where('id',$request->input('organisation'));
                });
            });
        }

        $booked_trades = $booked_trades_query->get();

        $booked_trades->transform(function($booked_trade) {
            return $booked_trade->preFormatAdmin(true);
        });

        if($booked_trades->count() <= 0) {
            return redirect()->back()->with('error', 'The selected csv download has no records');
        }

        $filename = Carbon::now()->format('Y_m_d_His')."_MM_booked_trades.csv";
        $handle = fopen($filename, 'w+');

        $booked_trade_keys = array_keys($booked_trades->first());
        // Map CSV collumn headings to a config defined heading
        $csv_headings = array_map( function($value) {
                return config('marketmartial.export_csv_field_mapping.booked_trade_fields.'.$value);
            },$booked_trade_keys);
        fputcsv($handle, $csv_headings);

        foreach ($booked_trades as $booked_trade) {
            fputcsv($handle, $booked_trade);            
        }

        $headers = ['Content-Type' => 'text/csv'];
        return response()->download($filename, $filename, $headers)->deleteFileAfterSend(true);


    }
}
