<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserManagement\Organisation;
use App\Http\Requests\UserRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\TermsofUseRequest;

class UserController extends Controller
{
   


    /**
     * load the users information to be updated
     *
     * @return Response
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        $organisations = Organisation::where('verified',true)
        ->orWhere(function($query) use ($user) {
            $query->whereHas('users',function($query) use ($user){
                $query->where('id',$user->id);
            });
        })
        ->pluck('title','id')
        ->toArray();

        // Used to determine admin profile update for the view
        $is_admin_update = false;
        return view('users.edit')->with(compact('user','organisations','is_admin_update'));
    }

    /**
     * Update the users information
     *
     * @return Response
     */
    public function update(UserRequest $request)
    {
        $data = $request->all();
        $user = $request->user();

        // Removed - Users are never able to change their organisations
        /*if(!$request->user()->completeProfile())
        {
            if( $request->has('new_organisation')) 
            {
                $data['organisation_id'] = Organisation::create([
                'title' => $data['new_organisation'],
                'verified' => false,
                ])->id;
            }
 
        }else
        {*/
            //dont allow organisation to be editable 
            if(array_key_exists('organisation',$data))
            {
                unset($data['organisation']);
            }
        //}

        //dont allow account email to be editable 
        if(array_key_exists('email',$data))
        {
            unset($data['email']);
        }

        $user->update($data);

        if( $user->completeProfile() ) {
            return redirect()->back()->with('success', 'Profile updated!');    
        }
        return redirect()->route($user->is_invited ? 'user.edit_password': 'email.edit')
            ->with('success', 'Profile updated!');
    }

    public function editPassword(Request $request)
    {
        $is_admin_update = false;
        return view('users.change_password')->with(compact('is_admin_update'));
    }


    public function storePassword(PasswordRequest $request)
    {
        $user = $request->user();
        $user->update(['password'=>bcrypt($request->input('password'))]);

        if(!$user->verifiedActiveUser() && !$user->completeProfile()) {
            \Cache::put('user_password_complete_'.$user->id, true,1440);
        }

        return $request->user()->completeProfile() ? redirect()->back()->with('success', 'Password updated!') : redirect()->route('email.edit')->with('success', 'Password updated!');
    }

    public function termsOfConditions(Request $request)
    {
        $user = $request->user();
        $is_admin_update = $user->isAdmin();
        return view('users.terms_and_conditions')->with(compact('user', 'is_admin_update'));
    }

    public function storeTermsAndConditions(TermsofUseRequest $request)
    {
        $user = $request->user();
        $user->tc_accepted = $request->input('tc_accepted');
        $user->update();
        return redirect()->back()->with('success', 'Terms and conditions have been accepted');
    }
}
