<?php
return [
    'default'   =>  "",
    // state when a UserMarketRequest is created but NO quotes exist
    'request'   => [
        'interest'      =>  "REQUEST-SENT",
        'other'         =>  "REQUEST",
    ],
    // state when a UserMarketRequest is created and quotes have been sent on it
    'request-vol'   =>  [
        'interest'          =>  "REQUEST-SENT-VOL",
        'other'             =>  "REQUEST-VOL",
    ],
    'negotiation-pending' => [
        'negotiator'        =>  "NEGOTIATION-VOL",
        'counter'           =>  "NEGOTIATION-VOL",
        'other'             =>  "NEGOTIATION-VOL-PENDING"
    ],
    'negotiation-open' => [
        'interest'        =>  "NEGOTIATION-OPEN-VOL",
        'market_maker'    =>  "NEGOTIATION-OPEN-VOL",
        'other'           =>  "NEGOTIATION-OPEN-VOL"
    ],
    'trade-negotiation-pending' => [
        'negotiator'      =>  "TRADE-NEGOTIATION-SENDER",
        'counter'         =>  "TRADE-NEGOTIATION-COUNTER",
        'other'           =>  "TRADE-NEGOTIATION-PENDING"
    ],
    'trade-negotiation-balance' => [
        'negotiator'      =>  "TRADE-NEGOTIATION-SENDER",
        'counter'         =>  "TRADE-NEGOTIATION-BALANCER",
        'other'           =>  "TRADE-NEGOTIATION-PENDING"
    ]
];