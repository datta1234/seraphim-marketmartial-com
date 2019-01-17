<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserManagement\Interest;
use App\Models\UserManagement\Email;
use App\Http\Requests\InterestRequest;

class InterestController extends Controller
{
    /**
     * Load the interest fields that can be completed
     *
     * @return Response
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $userInterests = $request->user()->interests()->get();
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
        $is_admin_update = false;
        return view('interest.edit')->with(compact('user','interests','is_admin_update'));
    }

    public function update(InterestRequest $request)
    {
        $user = $request->user();
        $user->update($request->all());
        // update the users interest
        $request->user()->interests()->sync($request->input('interest'));
        
        if(!$user->completeProfile()) {
            \Cache::put('user_interests_complete_'.$user->id, true,1440);
        }
        
        return $request->user()->completeProfile() ? redirect()->back()->with('success', 'Your interests have been updated.') : redirect()->route('tsandcs.edit')->with('success', 'Your interests have been updated.');
    }
}
