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

        if(!$request->user()->completeProfile())
        {
            if( $request->has('new_organisation')) 
            {
                $data['organisation_id'] = Organisation::create([
                'title' => $data['new_organisation'],
                'verified' => false,
                ])->id;
            }
 
        }else
        {
            //dont allow organisation_id to be editable 
            if(array_key_exists('organisation_id',$data))
            {
                unset($data['organisation_id']);
            }
        }
      

        $user->update($data);
        return $request->user()->completeProfile() ? redirect()->back()->with('success', 'Profile updated!') : redirect()->route('user.edit_password')->with('success', 'Profile updated!');
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
        return $request->user()->completeProfile() ? redirect()->back()->with('success', 'Password updated!') : redirect()->route('email.edit')->with('success', 'Password updated!');
    }

    public function termsOfConditions(Request $request)
    {
        $user = $request->user();
        return view('users.terms_and_conditions')->with(compact('user'));
    }

    public function storeTermsAndConditions(TermsofUseRequest $request)
    {
        $user = $request->user();
        $user->tc_accepted = $request->input('tc_accepted');
        $user->update();
        return redirect()->back()->with('success', 'Terms and Conditions updated!');
    }
}
