<?php

namespace App\Http\Controllers\TradeScreen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\ApiIntegration\SlackIntegration;
use App\Models\UserManagement\Organisation;

use App\Http\Requests\TradeScreen\SendSlackChatRequest;
use App\Http\Requests\TradeScreen\ReceiveSlackChatRequest;

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
        $message_history = $user->organisation->channelMessageHistory($user);

        if( $message_history === false ) {
            return response()->json(['data'=> null,'message'=>'An error occured retrieving the chat history, if the problem persists contact the admin.'], 500);
        }
        return response()->json(['data' => $message_history,'message' => 'Message sent.'], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SendSlackChatRequest $request)
    {
        $user = Auth::user();
        $response = null;
        if( $request->has('new_message') ) {
            $response = $user->organisation->sendMessage(str_replace(env('SLACK_ADMIN_REF'),"<@".env('SLACK_ADMIN_ID').">",$request->input('new_message')), $user->full_name, $user->organisation);
        } else {
            $response = $user->organisation->sendMessage("<@".env('SLACK_ADMIN_ID')."> ".$request->input('quick_message'), $user->full_name, $user->organisation);
        }
        if($response === false) {
            return response()->json(['data'=> null,'message'=>'Failed to send message, if the problem persists contact the admin.'], 500);
        } else {
            return response()->json([
                'data' => [
                    "user_name" => $response->message->username, 
                    "message" => str_replace("<@".env('SLACK_ADMIN_ID').">",env('SLACK_ADMIN_REF'),$response->message->text), 
                    "time_stamp" => $response->message->ts,
                    "status" => null
                ],
                'message' => 'Message sent.'
            ], 200);       
        }
    }

    public function receiveChat(ReceiveSlackChatRequest $request)
    {
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
                $organisation->receiveMessage($eventData, $organisation);
            }
        }
        return response("Received", 200);
    }
}
