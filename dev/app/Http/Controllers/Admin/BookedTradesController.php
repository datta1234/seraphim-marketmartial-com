<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TradeConfirmations\BookedTrade;
use App\Http\Requests\Admin\BookedTradeUpdateRequest;

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
                return [
                    'success' => true,
                    'data' => $booked_trade->preFormatAdmin(),
                    'message' => 'Booked Trade status successfully changed.'
                ];
            }
            return [
                'success' => false,
                'data' => null,
                'message' => 'Failed to change Booked Trade status.'
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
}
