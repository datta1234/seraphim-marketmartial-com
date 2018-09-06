<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserManagement\User;
use App\Http\Requests\Admin\UserStatusRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::where('role_id','<>', 1)->with('organisation','role')->paginate(10);
        if($request->ajax()) {
            return $users;
        }
        return view('admin.users.index')->with(['userData' => $users->toJson()]);
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
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
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
    public function update(UserStatusRequest $request, User $user)
    {
        if( $request->has('active') ) {
            // deactivate and reactivate logic
            $result = $user->update([
                'active' => $request->input('active'),
            ]);

            if($result){
                return [
                    'success' => true,
                    'data' => $user,
                    'message' => $request->input('active') ? 'User Reactivated.': 'User Deactivated.'
                ];
            }
            return [
                    'success' => false,
                    'data' => null,
                    'message' => $request->input('active') ? 'Failed to Reactivate the User.' : 'Failed to Deactivated the User .'
                ];
        }
        // verify and activate logic
        $result = $user->update([
            'verified' => $request->input('verified'),
            'active' => true,
        ]);

        // @TODO ADD ORG VERIFY AND CREATE SLACK CHANNEL LOGIC
        
        if($result){
            return ['success' => true, 'data' => $user, 'message' => 'User has been verified.'];
        }
        return ['success' => false, 'data' => null, 'message' => 'Failed to verify the user.'];
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
