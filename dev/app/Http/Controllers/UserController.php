<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserManagement\Organisation;
use App\Http\Requests\UserRequest;
use App\Http\Requests\PasswordRequest;

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
        $organisations = Organisation::all()->pluck('title','id')->toArray();
        return view('users.edit')->with(compact('user','organisations'));
    }

    /**
     * Update the users information
     *
     * @return Response
     */
    public function update(UserRequest $request)
    {
        $user = $request->user();
        $user->update($request->all());
        return redirect()->back()->with('success', 'Profile updated!');

    }

    public function editPassword(Request $request)
    {
        return view('users.change_password');
    }


    public function storePassword(PasswordRequest $request)
    {
        $user = $request->user();
        $user->update(['password'=>bcrypt($request->password)]);
        return redirect()->back()->with('success', 'Password updated!');
    }


}
