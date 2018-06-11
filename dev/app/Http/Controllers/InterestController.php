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
        $userInterest = $request->user()->interests()->get();
        $interests = Interest::whereNotIn('id',$userInterest->pluck('id'))->get()->merge($userInterest);
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
