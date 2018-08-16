<?php

namespace App\Traits;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

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
        $client = new Client();
        $response = $client->request('POST', env('SLACK_API_URL').'/groups.create', [
                'headers' => $header,
                'body'  =>  json_encode($body)
        ]);
        return json_decode($response->getBody());
    }

    public function sendMessage($message, $user_name)
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
            "channel" => $this->slack_channel->value
        ];
        $header = [
            "Authorization" => "Bearer ".env('SLACK_AUTH_BEARER'), 
            'Content-Type' =>'application/json', 
            'Accept' => 'application/json'
        ];
        $client = new Client();
        $response = $client->request('POST', env('SLACK_API_URL').'/chat.postMessage', [
                'headers' => $header,
                'body'  =>  json_encode($body)
        ]);
        return json_decode($response->getBody());
    }

    public function receiveMessage()
    {
        // User pusher to send new messages received from endpoint to channel users
    }
}