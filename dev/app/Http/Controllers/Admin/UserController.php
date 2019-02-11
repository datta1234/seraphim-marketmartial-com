<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserManagement\User;
use App\Models\UserManagement\Organisation;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\Session;
use App\Models\ApiIntegration\SlackIntegration;
use App\Http\Requests\Admin\UserStatusRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserRequest;

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
                ->paginate(50);

        $sessions = Session::whereIn('user_id', $users->pluck('id'))->get();

        $users->getCollection()->transform(function($user) use($sessions) {
            if( in_array($user->id,$sessions->pluck('user_id')->toArray()) ) {
                $user['is_online'] = true;
            } else {
                $user['is_online'] = false;
            }
            return $user;
        });

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
        $organisations = Organisation::all()->pluck('title','id')->toArray();
        $roles = Role::where('is_selectable',true)->get()->pluck('label','id')->toArray();
        return response()->json(['data' => ['roles'=> $roles, 'organisations'=> $organisations], 'message' => "Create user data loaded"]);
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
        $organisations = $user->organisation->pluck('title','id')->toArray();

        // Used to determine admin profile update for the view
        $is_admin_update = true;
        return view('users.edit')->with(compact('user','organisations','is_admin_update'));
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
        //@TODO - send email to user when state changes
        
        if( $request->has('active') ) {
            // deactivate and reactivate logic
            $user_update_result = $user->update([
                'active' => $request->input('active'),
            ]);

            if($user_update_result){
                if($request->ajax()) {
                    return response()->json([
                        'data' => $user,
                        'message' => $request->input('active') ? 'User Reactivated.': 'User Deactivated.'
                    ]);
                }
                return redirect()->back()->with('success', $request->input('active') ? 'User Reactivated.': 'User Deactivated.');
            }
            if($request->ajax()) {
                return response()->json([
                        'data' => null,
                        'message' => $request->input('active') ? 'Failed to Reactivate the User.' : 'Failed to Deactivated the User .'
                    ],500);
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
/*                // Creates the default brokerage fees for the organisation
                $user->organisation->setUpDefaultBrokerageFees();*/
                // verify the organisation if it is not verified and create Slack channel
                $organisation_update_result = $user->organisation->update([
                    'verified' => $request->input('verified'),
                ]);

                $slack_channel_data = $user->organisation->createChannel($user->organisation->channelName());
                // Failed to create slack channel
                if($slack_channel_data == false) {
                    if($request->ajax()) {
                        return response()->json(['data' => null, 'message' => 'Failed to create organisation slack channel.'],500);
                    }
                    return redirect()->back()->with('error', 'Failed to create organisation slack channel.'); 
                }

                if($slack_channel_data->ok == false) {
                    DB::commit();
                    throw new \App\Exceptions\SlackException("Failed to create organisation slack");
                }

                $slack_integration = new SlackIntegration([
                    "type"  => "string",
                    "field" => "channel",
                    "value" => $slack_channel_data->group->id,
                ]);
                $slack_integration->save();
                $user->organisation->slackIntegrations()->attach($slack_integration->id);

                Organisation::purgeCached();
            }
            DB::commit();

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            \Log::error($e);
            if($request->ajax()) {
                return response()->json(['data' => null, 'message' => 'Failed to verify the user.'],500);
            }
            return redirect()->back()->with('error', 'Failed to verify the user.');

        } catch (\App\Exceptions\SlackException $e) {
            \Log::error($e);
            if($request->ajax()) {
                return response()->json(['data' => null, 'message' => $e->getMessage()],500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }

        if($request->ajax()) {
            return response()->json(['data' => $user, 'message' => 'User has been verified.']);
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(UserRequest $request, User $user)
    {
        $data = $request->all();

        //dont allow organisation_id to be editable
        if(array_key_exists('organisation_id',$data))
        {
            unset($data['organisation_id']);
        }

        $user->update($data);
        return redirect()->back()->with('success', 'Profile updated!');
    }
}
