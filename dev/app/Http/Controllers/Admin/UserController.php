<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserManagement\User;
use App\Models\UserManagement\Organisation;
use App\Models\ApiIntegration\SlackIntegration;
use App\Http\Requests\Admin\UserStatusRequest;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::basicSearch(
                    $request->input('search'),
                    $request->input('_order_by') == '' ? null : $request->input('_order_by'),
                    $request->input('_order'),
                    $request->input('filter') == '' ? null : $request->input('filter'))
                ->where('role_id','<>', 1)
                ->select(DB::raw((new User)->getTable().".*, ".(new Organisation)->getTable().".title as 'organisation_title'"))
                ->join((new Organisation)->getTable(), (new Organisation)->getTable().'.id', '=', (new User)->getTable().'.organisation_id')
                ->with('organisation','role')
                ->paginate(10);

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
        return view('admin.users.show')->with(['user' => $user]);
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
            $user_update_result = $user->update([
                'active' => $request->input('active'),
            ]);

            if($user_update_result){
                if($request->ajax()) {
                    return [
                        'success' => true,
                        'data' => $user,
                        'message' => $request->input('active') ? 'User Reactivated.': 'User Deactivated.'
                    ];
                }
                return redirect()->back()->with('success', $request->input('active') ? 'User Reactivated.': 'User Deactivated.');
            }
            if($request->ajax()) {
                return [
                        'success' => false,
                        'data' => null,
                        'message' => $request->input('active') ? 'Failed to Reactivate the User.' : 'Failed to Deactivated the User .'
                    ];
            }
            return redirect()->back()->with('error', $request->input('active') ? 'Failed to Reactivate the User.' : 'Failed to Deactivated the User .');
        }


        try {
            DB::beginTransaction();
            
            // verify and activate logic
            $user->update([
                'verified' => $request->input('verified'),
                'active' => true,
            ]);

            if($user->organisation->verified == false) {
                // verify the organisation if it is not verified and create Slack channel
                $organisation_update_result = $user->organisation->update([
                    'verified' => $request->input('verified'),
                ]);

                $slack_channel_data = $user->organisation->createChannel($user->organisation->channelName());
                $slack_integration = new SlackIntegration([
                    "type"  => "string",
                    "field" => "channel",
                    "value" => $slack_channel_data->group->id,
                ]);
                $slack_integration->save();
                $user->organisation->slackIntegrations()->attach($slack_integration->id);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            if($request->ajax()) {
                return ['success' => false, 'data' => null, 'message' => 'Failed to verify the user.'];
            }
            return redirect()->back()->with('error', 'Failed to verify the user.');
        }
        if($request->ajax()) {
            return ['success' => true, 'data' => $user, 'message' => 'User has been verified.'];
        }
        return redirect()->back()->with('success', 'User has been verified.');

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
