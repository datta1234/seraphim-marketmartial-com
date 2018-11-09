<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserManagement\User;
use App\Models\UserManagement\DefaultLabel;
use App\Models\UserManagement\Email;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\EmailRequestStore;

class EmailController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmailRequestStore $request, User $user)
    {
        $email = new Email($request->all());
        $email->user_id = $user->id;
        $email->notifiable = false;
        $email->save();
       return response()->json(['data'=>$email,'message'=>'Email added.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
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
        $is_admin_update = true;
        return view('emails.edit')->with(compact('user','defaultLabels','emails','is_admin_update'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EmailRequest $request, User $user)
    {
        $emails = $request->input('email');
        $emailModels = [];
        //create or save the fields based on wether they have an ID 
        //get the already saved emails prevously

        $savedModels = $user->emails()->with('defaultLabel')->get();//get once that have alread been stored
        foreach ($emails as $key => $email) 
        {
            $emailModel = array_key_exists('id', $email) ? $savedModels->firstWhere('id',$email['id']) : New Email(); 
            $emailModel->fill($email);
            $emailModel->notifiable = in_array($emailModel->title, config('marketmartial.AutoSetTradeAccounts'));
            $emailModels[] = $emailModel;
        }
        $user->emails()->saveMany($emailModels);
        
       return response()->json([
           'data'=>[
                'email' => $user->emails()->with('defaultLabel')->get(),
                'redirect' => route('trade_settings.edit'),
            ],
            'message'=>'Emails updated.'
        ]);
    }
}
