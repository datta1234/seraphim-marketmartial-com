<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserManagement\Interest;
use App\Models\UserManagement\Email;
use App\Http\Requests\InterestRequest;

class InterestController extends Controller
{
        /**
     * load the email fields that can be completed
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
        return view('interest.edit')->with(compact('user','interests'));
    }

    public function update(InterestRequest $request)
    {
        $user = $request->user();
        $user->update($request->all());
        // update the users interest
        $request->user()->interests()->sync($request->input('interest'));
        
        return redirect()->back()->with('success', 'Interest have been updated!');

    }
}
