<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserManagement\Organisation;
use App\Models\UserManagement\BrokerageFee;

class BrokerageFeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organisations = Organisation::where('verified',true)->pluck('title','id');
        return view('admin.brokerage_fees.index')->with(['organisations' => $organisations->toJson()]);
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
        $brokerage_fees = BrokerageFee::where('organisation_id', $id)->get();

        if($brokerage_fees->isEmpty()) {
            $organisation = Organisation::find($id);

            if(is_null($organisation)) {
                return response()->json([
                    'message' => 'Organisation does not exists.',
                    'errors'  => []
                ],404);
            }

            $brokerage_fees = $organisation->setUpDefaultBrokerageFees();
        }

        return response()->json([
            'data' => $brokerage_fees,
            'message' => 'Brokerage Fees successfully loaded.'
        ]); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        dd($id,$request->all());
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
