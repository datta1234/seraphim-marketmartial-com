<?php

/**
 *
 * Biscolab Laravel ReCaptcha configuration file
 * MIT License @ https://github.com/biscolab/laravel-recaptcha/blob/master/LICENSE
 * author: Roberto Belotti - info@robertobelotti.com
 * web : robertobelotti.com, github.com/biscolab
 *
 * To configure correctly please visit https://developers.google.com/recaptcha/docs/start
 *
 */

return [
    'api_site_key'      => env('RECAPTCHA_SITE_KEY', ''),
    'api_secret_key'    => env('RECAPTCHA_SECRET_KEY', ''),
    'version'           => env('RECAPTCHA_VERSION', 'v2'), // supported: v3|v2|invisible 
    // @since v3.4.3
    'curl_timeout'      => env('RECAPTCHA_CURL_TIMEOUT', 10),
    'skip_ip'           => [], // array of IP addresses - String: dotted quad format e.g.: 127.0.0.1
    // @since v3.2.0
    'default_validation_route' => env('RECAPTCHA_DEFAULT_VALIDATION_ROUTE', 'biscolab-recaptcha/validate'),
    // @since v3.2.0
    'default_token_parameter_name' => env('RECAPTCHA_DEFAULT_TOKEN_PARAMETER_NAME', 'token')
];