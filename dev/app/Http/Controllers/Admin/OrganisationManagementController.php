<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserManagement\Organisation;

class OrganisationManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $organisations = Organisation::basicSearch(
                    $request->input('search'),
                    $request->input('_order_by') == '' ? null : $request->input('_order_by'),
                    $request->input('_order'),
                    $request->input('filter') == '' ? null : $request->input('filter'))
                ->paginate(50);

        if($request->ajax()) {
            return $organisations;
        }
        return view('admin.organisations.index')->with(['organisationData' => $organisations->toJson()]);
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
    public function update(Request $request, Organisation $organisation)
    {
        if( $request->has('slack_text_chat') && $request->input('slack_text_chat')) {
            // deactivate and reactivate logic
            $org_update_result = $organisation->update([
                'slack_text_chat' => !$organisation->slack_text_chat,
            ]);

            if($org_update_result){
                if($request->ajax()) {
                    return response()->json([
                        'data' => $organisation,
                        'message' => $organisation->slack_text_chat ? 'Chat Reactivated.': 'Chat Deactivated.'
                    ]);
                }
                return redirect()->back()->with('success', $organisation->slack_text_chat ? 'Chat Reactivated.': 'Chat Deactivated.');
            }
            if($request->ajax()) {
                return response()->json([
                        'data' => null,
                        'message' => $request->input('active') ? 'Failed to Reactivate the Organisation Chat.' : 'Failed to Deactivated the Organisation Chat.'
                    ],500);
            }
            return redirect()->back()->with('error', $request->input('active') ? 'Failed to Reactivate the User.' : 'Failed to Deactivated the User .');
        } else {
           if($request->ajax()) {
                return response()->json([
                        'data' => null,
                        'message' => 'Invalid organisation update request.'
                    ],406);
            }
            return redirect()->back()->with('error', 'Invalid organisation update request.'); 
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
