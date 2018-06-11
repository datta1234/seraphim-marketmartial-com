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
        $defaultLabels = DefaultLabel::whereNotIn('id',$emails->pluck('default_id'))->get(); 
        return view('emails.edit')->with(compact('user','defaultLabels','emails'));
    }

    public function store(EmailRequestStore $request)
    {
        $email = new Email($request->all());
        $email->user_id = $request->user()->id;
        $email->notifiable = false;
        $email->save();
       return ['success'=>'true','data'=>$email,'message'=>'email added'];
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
    	//create or save the fields based on wether they have an ID 
    	//get the already saved emails prevously

        $savedModels = $user->emails()->with('defaultLabel')->get();//get once that have alread been stored
    	foreach ($emails as $key => $email) 
    	{
    		$emailModel = array_key_exists('id', $email) ? $savedModels->firstWhere('id',$email['id']) : New Email(); 
    		$emailModel->fill($email);
    		$emailModels[] = $emailModel;
    	}
        $user->emails()->saveMany($emailModels);

       return ['success'=>'true','data'=>$user->emails()->with('defaultLabel')->get(),'message'=>'email added'];

    }
}
