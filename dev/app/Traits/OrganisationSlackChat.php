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
            $body = json_decode($response->getBody());
            
            if(isset($body->ok)) {
                return $body;
            }
            
            // Handle failure and get channel id if error is the following
            /*[2018-11-15 07:35:04] staging.INFO: array (
              'ok' => false,
              'error' => 'name_taken',
              'detail' => '`name` is already taken.',
              'warning' => 'missing_charset',
              'response_metadata' =>
              stdClass::__set_state(array(
                 'warnings' =>
                array (
                  0 => 'missing_charset',
                ),
              )),
            )*/
            if(isset($body->error) && $body->error == 'name_taken') {
                $channel = $this->findChannel($channel_name);
                if($channel != false) {
                    return ['ok' => true,'group' => $channel];
                }
                \Log::error(["Failed to find Oganisation Channel: ", (array)$body]);
            }

            \Log::error(["Oganisation Channel Failed to create: ", (array)$body]);
            return false;

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

    public function findChannel($channel_name)
    {
        $header = [
            "Authorization" => "Bearer ".env('SLACK_AUTH_BEARER'), 
            'Content-Type' =>'application/json', 
            'Accept' => 'application/json'
        ];
        try {
            $client = new Client();
            $response = $client->request('GET', env('SLACK_API_URL').'/groups.list', [
                    'headers' => $header,
            ]);
            $body = json_decode($response->getBody());
            
            if(isset($body->ok)) {
                if($body->ok == true) {
                    if(isset($body->groups) && is_array($body->groups)) {
                        $index = array_search($channel_name, array_column($body->groups, 'name'));
                        return $body->groups[$index];
                    }
                }
                return false;
            }

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
        if(!$organisation->slack_channel) {
            return false;
        }
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