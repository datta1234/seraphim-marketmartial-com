<?php
return [
    'quantity'  		=>  env('THRESHOLD_QUANTITY',500),
    'index_quantity'	=>  [
    	'1' 	=> env('TOP40_THRESHOLD_QUANTITY',500),
    	'2' 	=> env('DTOP_THRESHOLD_QUANTITY',2500),
    	'3' 	=> env('DCAP_THRESHOLD_QUANTITY',1500),
    ],
    'stock_quantity' 	=> env('STOCK_THRESHOLD_QUANTITY',50),
    'timeout'   		=> env('THRESHOLD_TIMEOUT',1200),
];