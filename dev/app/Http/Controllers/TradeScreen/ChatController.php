<?php

namespace App\Http\Controllers\TradeScreen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TradeScreen\SendSlackChatRequest;
use App\Models\ApiIntegration\SlackIntegration;
use App\Models\UserManagement\Organisation;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        // TODO add failure logic
        return [
            'success' => true,
            'data' => $user->organisation->channelMessageHistory(),
            'message' => 'Message sent.'
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SendSlackChatRequest $request)
    {
        if( $request->has('new_message') || $request->has('quick_message') ) {
            $user = Auth::user();
            $response = null;
            if( $request->has('new_message') ) {
                $response = $user->organisation->sendMessage(str_replace(env('SLACK_ADMIN_REF'),"<@".env('SLACK_ADMIN_ID').">",$request->input('new_message')), $user->full_name);
            } else {
                $response = $user->organisation->sendMessage("<@".env('SLACK_ADMIN_ID')."> ".$request->input('quick_message'), $user->full_name);
            }
            if($response->ok) {
                return [
                    'success' => true,
                    'data' => [
                        "user_name" => $response->message->username, 
                        "message" => str_replace("<@".env('SLACK_ADMIN_ID').">",env('SLACK_ADMIN_REF'),$response->message->text), 
                        "time_stamp" => $response->message->ts 
                    ],
                    'message' => 'Message sent.'
                ];       
            } else {
                // @TODO add logging to log the issue so admin can resolve or check the issue
                return ['success'=>false,'data'=> null,'message'=>'Failed to send message, retry and if the problem persists contact the admin.'];
            }
        }
        return ['success'=>false,'data'=> null,'message'=>'Invalid request.'];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function receiveChat(Request $request)
    {
        if( $request->has('token') && $request->input('token') === env('SLACK_AUTH_BEARER') ) {
            
            // Checks for slack challenge to set up endpoint to slack events
            if( $request->has('challenge') ) {
                return ['challenge'=>$request->input('challenge')];
            }

            // Checks if it is a new message event to send through pusher
            if( $request->has('event') ) {
                $eventData = $request->input('event');
                $organisation = Organisation::whereHas('slackIntegrations', function ($query) use ($eventData) {
                    $query->where([
                        ['field', 'channel'], 
                        ['value', $eventData["channel"]]
                    ]);
                })->first();

                // send message through pusher
                if($organisation !== null) {
                    $organisation->receiveMessage($eventData);
                }
                return response("received", 200);
            }
        }

        return response("Unauthorized", 401);
    }
}
