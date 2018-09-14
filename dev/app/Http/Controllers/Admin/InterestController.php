<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserManagement\User;
use App\Models\UserManagement\Interest;
use App\Http\Requests\InterestRequest;

class InterestController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $userInterests = $user->interests()->get();
        $interests = Interest::all();
        //we dont want to loose the order the interest are displayed in
        for ($i=0; $i < $interests->count() ; $i++) 
        { 
            $userInterest = $userInterests->firstWhere('id',$interests[$i]->id);
            if($userInterest)
            {
                $interests[$i] = $userInterest;
            }
        }

        // Used to determine admin interest update for the view
        $is_admin_update = true;
        return view('interest.edit')->with(compact('user','interests','is_admin_update'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InterestRequest $request, User $user)
    {
        $user->update($request->all());
        // update the users interest
        $user->interests()->sync($request->input('interest'));
        
        return redirect()->back()->with('success', 'Interest have been updated!');
    }
}
