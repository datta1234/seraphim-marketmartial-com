<?php
namespace App\Helpers\Slack;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use App\Events\ChatMessageReceived;

class Slack
{
    private $httpClient;
    private $baseUrl;
    private $authToken;

    function __construct() {
       $this->httpClient = new Client();
       $this->baseUrl = config('marketmartial.slack.api_url');
       $this->authToken = "Bearer ".config('marketmartial.slack.auth_bearer');
    }


    /**
    *   Send Request
    *   
    *   @param String $method
    *   @param Array $headers
    *   @param mixed $body
    */
    protected function sendHttp($method, $headers, $body) {
        
        // set auth token
        $headers['Authorization'] = $this->authToken;

        // send request
        try {
            $response = $this->httpClient->request($method, $this->baseUrl.'/chat.postMessage', [
                    'headers' => $headers,
                    'body'  =>  json_encode($body)
            ]);
            return $response->getBody();

        } catch(RequestException $e) {
            // handle issue
            \Log::error($e);
            $error_data = array("Request" => Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                $error_data["Response"] = Psr7\str($e->getResponse());
            }
            \Log::error(array( "Errors" => $error_data));
            return false;
        }
    }

    /**
     * Send message to Slack
     *
     * @return void
     */
    public function postMessage($content, $preset_as = null)
    {
        if($preset_as) {
            $presets = config('slack.preset_users');
            if( isset($presets[$preset_as]) ) {
                $content["as_user"] = false;
                $data = $presets[$preset_as];
                $content = array_merge($data, $content); // overwrite with input if need be
            }
        }
        $response = $this->sendHttp('POST',[
            'Content-Type' =>'application/json', 
            'Accept' => 'application/json'
        ],$content);

        return $response ? json_decode($response) : false;
    }

}