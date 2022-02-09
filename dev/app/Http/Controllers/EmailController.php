<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserManagement\DefaultLabel;
use App\Models\UserManagement\Email;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\EmailRequestStore;


class EmailController extends Controller
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
        $used = $emails->filter(function ($email, $key) {
            return $email->default_id !=  null;
        })->pluck('default_id');

        if($used)
        {
            $defaultLabels = DefaultLabel::whereNotIn('id',$used)->get(); 
        }
        
        // Used to determine admin interest update for the view
        $is_admin_update = false;
        return view('emails.edit')->with(compact('user','defaultLabels','emails','is_admin_update'));
    }

    public function store(EmailRequestStore $request)
    {
        $email = new Email($request->all());
        $email->user_id = $request->user()->id;
        $email->notifiable = true;
        $email->save();
       return response()->json(['data'=>$email,'message'=>'Email added.']);
    }

    /**
     * Update the users email fields
     *
     * @return Response
     */
    public function update(EmailRequest $request)
    {
        $user = $request->user();
    	$emails = $request->input('email');
    	$emailModels = [];
        $defaultLabels = [];
    	//create or save the fields based on weather they have an ID 
    	//get the already saved emails prevously

        $savedModels = $user->emails()->with('defaultLabel')->get();//get once that have alread been stored
    	foreach ($emails as $key => $email) 
    	{
    		$emailModel = array_key_exists('id', $email) ? $savedModels->firstWhere('id',$email['id']) : New Email(); 
    		$emailModel->fill($email);
            /*$emailModel->notifiable = in_array($emailModel->title, config('marketmartial.AutoSetTradeAccounts'));*/
            // Phase 3 update - All accounts should be notified even the custom accounts
            $emailModel->notifiable = true;
            
            if(empty($emailModel->email)) {
                $defaultLabels[] = $emailModel;
                $emailModel->delete();
            } else {
        		$emailModels[] = $emailModel;
            }
    	}
        $user->emails()->saveMany($emailModels);
        
        return response()->json([
        'data'=>[
            'email' => $user->emails()->with('defaultLabel')->get(),
            'defaultLabels' => $defaultLabels,
            'redirect' => route('trade_settings.edit')
        ],
        'message'=>'Emails updated.']);

    }
}
