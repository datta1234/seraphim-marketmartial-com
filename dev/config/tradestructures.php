<?php 
        //fly selected, outright -> force yes
        //efp switch, risky, caly,options swicth, -> no force select
return [
    [
        // __trade_structure__
        "id" => 1,
        "title" => "Outright",
        "is_selectable" => true,
        "trade_structure_group" =>
        [
            [
                // __user_market_request_group__
                "id" => 1,
                "title"=> "default",
                "force_select" => true,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 1
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 2
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 3
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                ],
                "trade_confirmation_group"=> [
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 4
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 5
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 6
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 7
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 8
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 9
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 10
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 11
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 12
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]                                
            ]
        ],
    ],
    [
        // __trade_structure__
        "id" => 2,
        "title" => "Risky",
        "is_selectable" => true,
        "trade_structure_group" =>
        [
            [
                // __user_market_request_group__
                "id" => 2,
                "title"=> "default",
                "force_select" => null,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 13
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 14
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 15
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 16
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 17
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 18
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 19
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 20
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 21
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 22
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 23
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 24
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]

            ],
            [
                // __user_market_request_group__
                "id" => 3,
                "title"=> "Risky options",
                "force_select" => null,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 25
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 26
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 27
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 28
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 29
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 30
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 31
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 32
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 33
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 34
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 35
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 36
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]

            ]
        ],
    ],
    [
        // __trade_structure__
        "id" => 3,
        "title" => "Calendar",
        "is_selectable" => true,
        "trade_structure_group" =>
        [
            [
                // __user_market_request_group__
                "id" => 4,
                "title"=> "default",
                "force_select" => null,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 37
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 38
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 39
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 40
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 41
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 42
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 43
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 44
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 45
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 46
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 47
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 48
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]
            ],
            [
                // __user_market_request_group__
                "id" => 5,
                "title"=> "Calander option",
                "force_select" => null,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 49
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 50
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 51
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 52
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 53
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 54
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 55
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 56
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 57
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 58
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 59
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 60
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]
            ]
        ],
        
    ],
    [
        // __trade_structure__
        "id" => 4,
        "title" => "Fly",
        "is_selectable" => true,
        "trade_structure_group" => [
            [
                // __user_market_request_group__
                "id" => 6,
                "title"=> "default",
                "force_select" => true,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 61
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 62
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 63
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 64
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 65
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 66
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 67
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 68
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 69
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 70
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 71
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 72
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]
            ],
            [
                // __user_market_request_group__
                "id" => 7,
                "title"=> "Fly Second option",
                "force_select" => false,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 73
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 74
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 75
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 76
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 77
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 78
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 79
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 80
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 81
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 82
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 83
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]
            ],
            [
                // __user_market_request_group__
                "id" => 8,
                "title"=> "Fly third option",
                "force_select" => true,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 84
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 85
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 86
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 87
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 88
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 89
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 90
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 91
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 92
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 93
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 94
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]
            ]
        ],
        
    ],
    [
        // __trade_structure__
        "id" => 5,
        "title" => "Option Switch",
        "is_selectable" => false,
        "trade_structure_group" => [
            [
                // __user_market_request_group__
                "id" => 8,
                "title"=> "default",
                "force_select" => null,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 95
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 96
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 97
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 98
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 99
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 100,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 101,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 102,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 103,
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 104,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 105,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 106,
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]
            ],
            [
                // __user_market_request_group__
                "id" => 9,
                "title"=> "Switch Options",
                "force_select" => null,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 107,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 108,
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 109,
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 110,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 111,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 112,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 113,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 114,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 115,
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 116,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 117,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 118,
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]
            ]
        ],
        
    ],
    [
        // __trade_structure__
        "id" => 6,
        "title" => "EFP",
        "is_selectable" => false,
        "trade_structure_group" => [
            [
                // __user_market_request_group__
                "id" => 10,
                "title"=> "default",
                "force_select" => true,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 119,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 120,
                        'title' => 'Quantity',
                        'type' => 'double'
                    ],

                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 121,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 122,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 123,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 124,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 125,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 126,
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 127,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 128,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 129,
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]
            ]
        ],
        
    ],
    [
        // __trade_structure__
        "id" => 7,
        "title" => "Rolls",
        "is_selectable" => false,
        "trade_structure_group" => [
            [
                // __user_market_request_group__
                "id" => 11,
                "force_select" => true,
                "trade_structure_group_type_id" => 1,
                "title"=> "default",
                "items"=>
                [
                    [
                        // __item__
                        "id" => 130,
                        "title" => "Expiration Date 1",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 131,
                        "title" => "Expiration Date 2",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 132,
                        'title' => 'Quantity',
                        'type' => 'double'
                    ],
                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 133,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 134,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 135,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 136,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 137,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 138,
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 139,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 140,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 141,
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]
            ]
        ],
        
    ],
    [
        // __trade_structure__
        "id" => 8,
        "title" => "EFP Switch",
        "is_selectable" => false,
        "trade_structure_group" => [
            [
                // __user_market_request_group__
                "id" => 12,
                "title"=> "default",
                "force_select" => null,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 142,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 143,
                        'title' => 'Quantity',
                        'type' => 'double'
                    ],
                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" =>
                        [
                            [
                                // __item__
                                "id" => 144,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 145,
                                "title"=>"Put/Call",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 146,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 147,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 148,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 149,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 150,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 151,
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]
            ],
            [
                // __user_market_request_group__
                "id" => 13,
                "title"=> "Switch Options",
                "force_select" => null,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 152,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 153,
                        'title' => 'Quantity',
                        'type' => 'double'
                    ],
                ],
                "trade_confirmation_group"=>[
                    "options" =>
                    [
                        "title" => "Options Group",
                        "items" =>
                        [
                            [
                                // __item__
                                "id" => 154,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 155,
                                "title"=>"is_put",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 156,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 157,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 158,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                        ]
                    ],
                    "futures" =>
                    [
                        "title" => "Futures Group",
                        "items" => [
                            [
                                // __item__
                                "id" => 159,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 160,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 161,
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]

            ]
        ],
        
    ]
];
