<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserManagement\DefaultLabel;
use App\Models\UserManagement\Email;

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
        $emails = $user->emails()->with('defaultLabel')->get(); 
        // only get defaults if
        $defaultLabels = DefaultLabel::whereNotIn('id', $emails->pluck('default_id'))->get(); 
        return view('interest.edit')->with(compact('user','defaultLabels','emails'));
    }
}
