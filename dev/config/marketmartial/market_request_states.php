<?php
return [
    'default'   =>  "",
    // state when a UserMarketRequest is created but NO quotes exist
    'request'   => [
        'interest'  => "REQUEST-SENT",
        'other'     => "REQUEST",
    ],
    // state when a UserMarketRequest is created and quotes have been sent on it
    'request-vol'   =>  [
        'interest'  =>  "REQUEST-SENT-VOL",
        'other'     =>  "REQUEST-VOL",
    ],
];