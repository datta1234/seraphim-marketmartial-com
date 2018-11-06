<?php

namespace App\Traits;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use App\Events\ChatMessageReceived;

trait OrganisationSlackChat {

    public function createChannel($channel_name)
    {
        /*Creates a new private channel on slack for organisation
        url - https://slack.com/api/groups.create
        header - Authorization : Bearer $bearer_auth_key
        body -  {
                    "validate": true,
                    "name": $channel_name
                }
        */
        $body = [
            "validate" => true,
            "name" => $channel_name
        ];
        $header = [
            "Authorization" => "Bearer ".env('SLACK_AUTH_BEARER'), 
            'Content-Type' =>'application/json', 
            'Accept' => 'application/json'
        ];
        try {
            $client = new Client();
            $response = $client->request('POST', env('SLACK_API_URL').'/groups.create', [
                    'headers' => $header,
                    'body'  =>  json_encode($body)
            ]);
            return json_decode($response->getBody());

        } catch(RequestException $e) {
            \Log::error($e);
            $error_data = array("Request" => Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                $error_data["Response"] = Psr7\str($e->getResponse());
            }
            \Log::error(array( "Errors" => $error_data));
            return false;
        }
    }

    public function sendMessage($message, $user_name, $organisation)
    {
        /*logic to send a message to slack channel url
        url - https://slack.com/api/chat.postMessage
        header - Authorization : Bearer $bearer_auth_key
        body -  {
                    "text": $message,
                    "as_user": false,
                    "username": $user_name,
                    "channel": $slack_organisation_channel
                }
        */
        $body = [
            "text" => $message,
            "as_user" => false,
            "username" => $user_name,
            "channel" => $organisation->slack_channel->value
        ];
        $header = [
            "Authorization" => "Bearer ".env('SLACK_AUTH_BEARER'), 
            'Content-Type' =>'application/json', 
            'Accept' => 'application/json'
        ];
        $response = null;

        try {
            $client = new Client();
            $response = $client->request('POST', env('SLACK_API_URL').'/chat.postMessage', [
                    'headers' => $header,
                    'body'  =>  json_encode($body)
            ]);

        } catch(RequestException $e) {
            \Log::error($e);
            $error_data = array("Request" => Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                $error_data["Response"] = Psr7\str($e->getResponse());
            }
            \Log::error(array( "Errors" => $error_data));
            return false;
        }
        return json_decode($response->getBody());
    }

    public function channelMessageHistory($user)
    {
        $header = [
            "Authorization" => "Bearer ".env('SLACK_AUTH_BEARER'), 
            'Content-Type' =>'application/json', 
            'Accept' => 'application/json'
        ];
        $response = null;
        // no slack channel set up
        if(!$user->organisation->slack_channel) {
            // @TODO: Notify admin of fault?
            return false;
        }
        try {
            $client = new Client();
            $response = json_decode($client->request('GET', env('SLACK_API_URL').'/groups.history?channel='.$user->organisation->slack_channel->value, [
                    'headers' => $header,
            ])->getBody());

        } catch(RequestException $e) {
            \Log::error($e);
            $error_data = array("Request" => Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                $error_data["Response"] = Psr7\str($e->getResponse());
            }
            \Log::error(array( "Errors" => $error_data));
            return false;
        } catch(ErrorException $e) {
            \Log::error($e);
            return false;
        }  catch(\Exception $e) {
            \Log::error($e);
            return false;
        }


        $formatted_messages = array();
        if(!isset($response->messages)) {
            return false;
        }
        foreach ($response->messages as $message) {
            if($message->type === 'message') {
                if(property_exists($message,'subtype') && $message->subtype === 'bot_message') {
                    $formatted_messages[] = (object) array(
                        "user_name" => $message->username,
                        "message" => str_replace("<@".env('SLACK_ADMIN_ID').">",env('SLACK_ADMIN_REF'), $message->text),
                        "time_stamp" => $message->ts,
                        "status" => ($user->full_name == $message->username ? 'received' : null) 
                    );
                } elseif(property_exists($message,'user') && $message->user === env('SLACK_ADMIN_ID') && !property_exists($message,'subtype')) {
                    $formatted_messages[] = (object) array(
                        "user_name" => "Market Martial",
                        "message" => $message->text,
                        "time_stamp" => $message->ts,
                        "status" => null
                    );
                }
            }
        }
        return array_reverse($formatted_messages);    
    }

    public function receiveMessage($eventData, $organisation)
    {
        if($eventData["type"] === 'message') {
            if(array_key_exists('subtype',$eventData) && $eventData["subtype"] === 'bot_message') {
                $formatted_message_bot = array(
                    "user_name" => $eventData["username"],
                    "message" => str_replace("<@".env('SLACK_ADMIN_ID').">",env('SLACK_ADMIN_REF'), $eventData["text"]),
                    "time_stamp" => $eventData["ts"],
                    "status" => null
                );
                
                event(new ChatMessageReceived($organisation,$formatted_message_bot));
            } elseif(array_key_exists('user',$eventData) && $eventData["user"] === env('SLACK_ADMIN_ID') && !array_key_exists('subtype',$eventData)) {
                $formatted_message_admin = array(
                    "user_name" => "Market Martial",
                    "message" => $eventData["text"],
                    "time_stamp" => $eventData["ts"],
                    "status" => null
                );
                
                event(new ChatMessageReceived($organisation,$formatted_message_admin));
            }
        }
    }
}