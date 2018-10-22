<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Trade\Rebate;
use App\Models\UserManagement\User;
use App\Models\UserManagement\Organisation;
use App\Http\Requests\Admin\RebateUpdateRequest;

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
}
