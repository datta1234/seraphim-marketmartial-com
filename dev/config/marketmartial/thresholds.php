<?php
return [
    'quantity'  		=>  env('THRESHOLD_QUANTITY',500),
    'index_quantity'	=>  [
    	'1' 	=> env('TOP40_THRESHOLD_QUANTITY',500),
    	'2' 	=> env('DTOP_THRESHOLD_QUANTITY',2500),
    	'3' 	=> env('DCAP_THRESHOLD_QUANTITY',1500),
    ],
    'var_swap_quantity' => env('VAR_SWAP_THRESHOLD_QUANTITY',500000),
    'stock_quantity' 	=> env('STOCK_THRESHOLD_QUANTITY',50),
    'condition_timeouts'    =>  [
        // Timeouts In Mins
        'cond_fok_spin'   =>  20,
        'cond_buy_best'   =>  15,
    ],
    // Lock time in Seconds
    'lock_timeout'        => 30,
];