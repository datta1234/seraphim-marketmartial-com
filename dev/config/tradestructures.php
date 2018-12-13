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
                "force_select" => false,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 1,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 2,
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 3,
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                    [
                        // __item__
                        "id" => 4,
                        "title" => "Future",
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
                                "id" => 5,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 6,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 7,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 8,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 9,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 10,
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
                                "id" => 11,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 12,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 13,
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
                        "id" => 14,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 15,
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 16,
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                    [
                        // __item__
                        "id" => 17,
                        "title" => "Future",
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
                                "id" => 18,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 19,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 20,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 21,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 22,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 23,
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
                                "id" => 24,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 25,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 26,
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
                        "id" => 27,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 28,
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 29,
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
                                "id" => 30,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 31,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 32,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 33,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 34,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 35,
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
                                "id" => 36,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 37,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 38,
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
                        "id" => 39,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 40,
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 41,
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                    [
                        // __item__
                        "id" => 42,
                        "title" => "Future",
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
                                "id" => 43,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 44,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 45,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 46,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 47,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 48,
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
                                "id" => 49,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 50,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 51,
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
                        "id" => 52,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 53,
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 54,
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                    [
                        // __item__
                        "id" => 55,
                        "title" => "Future",
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
                                "id" => 56,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 57,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 58,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 59,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 60,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 61,
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
                                "id" => 62,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 63,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 64,
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
                        "id" => 65,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 66,
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 67,
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                    [
                        // __item__
                        "id" => 68,
                        "title" => "Future",
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
                                "id" => 69,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 70,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 71,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 72,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 73,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 74,
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
                                "id" => 75,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 76,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 77,
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
                        "id" => 78,
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 79,
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
                                "id" => 80,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 81,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 82,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 83,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 84,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 85,
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
                                "id" => 86,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 87,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 88,
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
                        "id" => 89,
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 90,
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
                                "id" => 91,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 92,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 93,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 94,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 95,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 96,
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
                                "id" => 97,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 98,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 99,
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
                        "id" => 100,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 101,
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 102,
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                    [
                        // __item__
                        "id" => 103,
                        "title" => "Future",
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
                                "id" => 104,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 105,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 106,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 107,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 108,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 109,
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
                                "id" => 110,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 111,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 112,
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
                        "id" => 113,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 114,
                        "title" => "Strike",
                        "type" => "double" 

                    ],
                    [
                        // __item__
                        "id" => 115,
                        "title" => "Quantity",
                        "type" => "double"   
                    ],
                    [
                        // __item__
                        "id" => 116,
                        "title" => "Future",
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
                                "id" => 117,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 118,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 119,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 120,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 121,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 122,
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
                                "id" => 123,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 124,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 125,
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
                "force_select" => false,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 126,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 127,
                        'title' => 'Quantity',
                        'type' => 'double'
                    ],
                    [
                        // __item__
                        "id" => 128,
                        "title" => "Future",
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
                                "id" => 129,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 130,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 131,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 132,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 133,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 134,
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
                                "id" => 135,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 136,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 137,
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
                "force_select" => false,
                "trade_structure_group_type_id" => 1,
                "title"=> "default",
                "items"=>
                [
                    [
                        // __item__
                        "id" => 138,
                        "title" => "Expiration Date 1",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 139,
                        "title" => "Expiration Date 2",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 140,
                        'title' => 'Quantity',
                        'type' => 'double'
                    ],
                    [
                        // __item__
                        "id" => 141,
                        "title" => "Future 1",
                        "type" => "double"   
                    ],
                    [
                        // __item__
                        "id" => 142,
                        "title" => "Future 2",
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
                                "id" => 143,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 144,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 145,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 146,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 147,
                                "title"=>"Net Premiums" ,
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 148,
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
                    [
                        // __item__
                        "id" => 154,
                        "title" => "Future",
                        "type" => "double"   
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
                                "id" => 155,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 156,
                                "title"=>"Put/Call",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 157,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 158,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 159,
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
                                "id" => 160,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 161,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 162,
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
                        "id" => 163,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 164,
                        'title' => 'Quantity',
                        'type' => 'double'
                    ],
                    [
                        // __item__
                        "id" => 165,
                        "title" => "Future",
                        "type" => "double"   
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
                                "id" => 166,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 167,
                                "title"=>"is_put",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 168,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 169,
                                "title"=>"Gross Premiums",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 170,
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
                                "id" => 171,
                                "title"=>"is_offer",
                                "type"=> "boolean"
                            ],
                            [
                                // __item__
                                "id" => 172,
                                "title"=>"Future",
                                "type"=> "double"
                            ],
                            [
                                // __item__
                                "id" => 173,
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
        "id" => 9,
        "title" => "Var Swap",
        "is_selectable" => true,
        "trade_structure_group" => [
            [
                // __user_market_request_group__
                "id" => 14,
                "title"=> "default",
                "force_select" => false,
                "trade_structure_group_type_id" => 1,
                "items"=>
                [
                    [
                        // __item__
                        "id" => 174,
                        "title" => "Expiration Date",
                        "type" => "expiration date"   
                    ],
                    [
                        // __item__
                        "id" => 175,
                        'title' => 'Quantity',
                        'type' => 'double'
                    ],
                    [
                        // __item__
                        "id" => 176,
                        'title' => 'Cap',
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
                                "id" => 177,
                                "title"=>"is_offer",
                                "type"=>"boolean"
                            ],
                            [
                                // __item__
                                "id" => 178,
                                "title"=>"is_put",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 179,
                                "title"=>"Volatility",
                                "type"=>"double"
                            ],
                            [
                                // __item__
                                "id" => 180,
                                "title"=>"Contract",
                                "type"=> "double"
                            ]
                        ]
                    ]
                ]
            ]
        ],
        
    ],
];
