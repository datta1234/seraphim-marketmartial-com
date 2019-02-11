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
    "safex_validation" => [
        'rules' => [
            'trade_id'        => 'required|numeric',
            'underlying'      => 'required|string|max:255',
            'trade_date'      => 'required|date',
            'structure'       => 'required|string|max:255',
            'underlying_alt'  => 'required|string|max:255',
            'strike'          => 'required|numeric',
            /*'strike_percentage' => '',*/
            'is_put'          => 'required|in:P,C',
            'volspread'       => 'required',
            'expiry'          => 'required|date',
            'nominal'         => 'required|numeric',
        ],
        'messages' => [
            'trade_id.required'       => 'The Trade ID field is required.',
            'trade_id.numeric'        => 'The Trade ID field must be a numberic value.',
            'underlying.required'     => 'The Name field is required.',
            'underlying.string'       => 'The Name field must be a string value.',
            'underlying.max'          => 'The Name field has a max length of :max.',
            'trade_date.required'     => 'The Trade Date field is required.',
            'trade_date.date'         => 'The Trade Date is not a valid date.',
            'structure.required'      => 'The Structure field is required.',
            'structure.string'        => 'The Structure field must be a string value.',
            'structure.max'           => 'The Structure field has a max length of :max.',
            'underlying_alt.required' => 'The Underlying field is required.',
            'underlying_alt.string'   => 'The Underlying field must be a string value.',
            'underlying_alt.max'      => 'The Underlying field has a max length of :max.',
            'strike.required'         => 'The Strike field is required.',
            'strike.numeric'          => 'The Strike field must be a numberic value.',
            'is_put.required'         => 'The Put/Call field is required.',
            'is_put.in'               => 'The Put/Call field must either be a P or C character',
            'volspread.required'      => 'The Vol field is required.',
            'expiry.required'         => 'The Expiry field is required.',
            'expiry.date'             => 'The Expiry is not a valid date.',
            'nominal.required'        => 'The Nominal field is required.',
            'nominal.numeric'         => 'The Nominal field must be a numberic value.',
        ]
    ],
    "open_interest_validation" => [
        'rules' => [
            'market_name'     => 'required|string|max:255',
            'contract'        => 'required|string|max:255',
            'expiry_date'     => 'required|date',
            'is_put'          => 'required|in:P,C',
            'strike_price'    => 'required|numeric',
            'open_interest'   => 'required|numeric',
            'delta'           => 'required|numeric',
            // changed due to field type change [MM-811]
            /*'spot_price'      => 'required|numeric'*/
            'spot_price'      => 'required|string|max:255'
        ],
        'messages' => [
            'market_name.required'    => 'The Market Name field is required.',
            'market_name.string'      => 'The Market Name field must be a string value.',
            'market_name.max'         => 'The Market Name field has a max length of :max.',
            'contract.required'       => 'The Contract field is required.',
            'contract.string'         => 'The Contract field must be a string value.',
            'contract.max'            => 'The Contract field has a max length of :max.',
            'expiry_date.required'    => 'The ExpiryDate field is required.',
            'expiry_date.date'        => 'The ExpiryDate is not a valid date.',
            'is_put.required'         => 'The Put/Call field is required',
            'is_put.in'               => 'The Put/Call field must either be a P or C character',
            'strike_price.required'   => 'The Strike Price field is required',
            'strike_price.numeric'    => 'The Strike Price field must be a numberic value.',
            'open_interest.required'  => 'The Open Interest field is required',
            'open_interest.numeric'   => 'The Open Interest field must be a numberic value.',
            'delta.required'          => 'The Delta field is required',
            'delta.numeric'           => 'The Delta field must be a numberic value.',
            'spot_price.required'     => 'The Spot Price field is required',
            // changed due to field type change [MM-811]
            /*'spot_price.numeric'      => 'The Spot Price field must be a numberic value.'*/
            'spot_price.string'      => 'The Spot Price field must be a string value.',
            'spot_price.max'         => 'The Spot Price field has a max length of :max.'
        ]
    ],
];