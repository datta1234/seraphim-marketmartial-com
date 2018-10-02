<?php
return [
    "safex_fields" => [
        'Trade ID'          => 'trade_id',
        'Name'              => 'underlying',
        'Trade Date'        => 'trade_date',
        'Structure'         => 'structure',
        'Underlying'        => 'underlying_alt',
        'Strike'            => 'strike',
        'Strike%'           => 'strike_percentage',
        'Put/Call'          => 'is_put',
        'Vol'               => 'volspread',
        'Expiry'            => 'expiry',
        'Nominal'           => 'nominal',
    ],
    "open_interest_fields"  => [
        'Market Name'       => 'market_name',
        'Contract'          => 'contract',
        'ExpiryDate'        => 'expiry_date',
        'Put/Call'          => 'is_put',
        'Strike Price'      => 'strike_price',
        'Open Interest'     => 'open_interest',
        'Delta'             => 'delta',
        'Spot Price'        => 'spot_price',
    ],
];