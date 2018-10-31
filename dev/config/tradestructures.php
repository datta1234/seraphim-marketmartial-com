<?php 
        //fly selected, outright -> force yes
        //efp switch, risky, caly,options swicth, -> no force select
return [
        [
            'id' => 1,
            'title' => 'Outright',
            'is_selectable' => true,
            'trade_structure_group' =>
                    [
                        [
                            'title'=> 'default',
                            'force_select' => true,
                            'items'=>
                                [
                                    [
                                        'title' => 'Expiration Date',
                                        'type' => 'expiration date'   
                                    ],
                                    [
                                        'title' => 'Strike',
                                        'type' => 'double' 

                                    ],
                                    [
                                        'title' => 'Quantity',
                                        'type' => 'double'   
                                    ],
                                ],
                                "trade_confirmation_group"=>[
                                    "options" =>
                                     [
                                        "items" => [
                                            [
                                                "title"=>"is_offer",
                                                "type"=>"boolean"
                                            ],
                                            [
                                                "title"=>"Put",
                                                "type"=>"double"
                                            ],
                                            [
                                                'title' =>'Call',
                                                'type'  =>'double'
                                            ],
                                            [
                                                "title"=>"Volatility",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Gross Premiums",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Net Premiums" ,
                                                "type"=>"double"
                                            ],
                                            [
                                            "title"=>"Contract",
                                            "type"=> "double"
                                            ]
                                        ]
                                    ],
                                        "futures" =>
                                        [
                                            "items" => [
                                                [
                                                    "title"=>"is_offer",
                                                    "type"=> "boolean"
                                                ],
                                                [
                                                    "title"=>"Future",
                                                    "type"=> "double"
                                                ],
                                                [
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
            'id' => 2,
            'title' => 'Risky',
            'is_selectable' => true,

                     'trade_structure_group' =>
                        [
                            [
                                'title'=> 'default',
                                'force_select' => null,
                                'items'=>
                                    [
                                        [
                                            'title' => 'Expiration Date',
                                            'type' => 'expiration date'   
                                        ],
                                        [
                                            'title' => 'Strike',
                                            'type' => 'double' 

                                        ],
                                        [
                                            'title' => 'Quantity',
                                            'type' => 'double'   
                                        ],
                                    ],
                                    "trade_confirmation_group"=>[
                                        "options" =>
                                     [
                                        "items" => [
                                            [
                                                "title"=>"is_offer",
                                                "type"=>"boolean"
                                            ],
                                            [
                                                "title"=>"Put",
                                                "type"=>"double"
                                            ],
                                            [
                                                'title' =>'Call',
                                                'type'  =>'double'
                                            ],
                                            [
                                                "title"=>"Volatility",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Gross Premiums",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Net Premiums" ,
                                                "type"=>"double"
                                            ],
                                            [
                                            "title"=>"Contract",
                                            "type"=> "double"
                                            ]
                                        ]
                                    ],
                                        "futures" =>
                                        [
                                            "items" => [
                                                [
                                                    "title"=>"is_offer",
                                                    "type"=> "boolean"
                                                ],
                                                [
                                                    "title"=>"Future",
                                                    "type"=> "double"
                                                ],
                                                [
                                                    "title"=>"Contract",
                                                    "type"=> "double"
                                                ]
                                            ]
                                        ]
                                    ]

                            ],
                            [
                                'title'=> 'Risky options',
                                'force_select' => null,
                                'items'=>
                                    [
                                        [
                                            'title' => 'Expiration Date',
                                            'type' => 'expiration date'   
                                        ],
                                        [
                                            'title' => 'Strike',
                                            'type' => 'double' 

                                        ],
                                        [
                                            'title' => 'Quantity',
                                            'type' => 'double'   
                                        ],
                                    ],
                                     "trade_confirmation_group"=>[
                                        "options" =>
                                     [
                                        "items" => [
                                            [
                                                "title"=>"is_offer",
                                                "type"=>"boolean"
                                            ],
                                            [
                                                "title"=>"Put",
                                                "type"=>"double"
                                            ],
                                            [
                                                'title' =>'Call',
                                                'type'  =>'double'
                                            ],
                                            [
                                                "title"=>"Volatility",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Gross Premiums",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Net Premiums" ,
                                                "type"=>"double"
                                            ],
                                            [
                                            "title"=>"Contract",
                                            "type"=> "double"
                                            ]
                                        ]
                                    ],
                                        "futures" =>
                                        [
                                            "items" => [
                                                [
                                                    "title"=>"is_offer",
                                                    "type"=> "boolean"
                                                ],
                                                [
                                                    "title"=>"Future",
                                                    "type"=> "double"
                                                ],
                                                [
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
            'id' => 3,
            'title' => 'Calendar',
            'is_selectable' => true,
              'trade_structure_group' =>
                        [
                            [
                                'title'=> 'default',
                                'force_select' => null,
                                'items'=>
                                    [
                                        [
                                            'title' => 'Expiration Date',
                                            'type' => 'expiration date'   
                                        ],
                                        [
                                            'title' => 'Strike',
                                            'type' => 'double' 

                                        ],
                                        [
                                            'title' => 'Quantity',
                                            'type' => 'double'   
                                        ],
                                    ],
                                 "trade_confirmation_group"=>[
                                        "options" =>
                                     [
                                        "items" => [
                                            [
                                                "title"=>"is_offer",
                                                "type"=>"boolean"
                                            ],
                                            [
                                                "title"=>"Put",
                                                "type"=>"double"
                                            ],
                                            [
                                                'title' =>'Call',
                                                'type'  =>'double'
                                            ],
                                            [
                                                "title"=>"Volatility",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Gross Premiums",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Net Premiums" ,
                                                "type"=>"double"
                                            ],
                                            [
                                            "title"=>"Contract",
                                            "type"=> "double"
                                            ]
                                        ]
                                    ],
                                        "futures" =>
                                        [
                                            "items" => [
                                                [
                                                    "title"=>"is_offer",
                                                    "type"=> "boolean"
                                                ],
                                                [
                                                    "title"=>"Future",
                                                    "type"=> "double"
                                                ],
                                                [
                                                    "title"=>"Contract",
                                                    "type"=> "double"
                                                ]
                                            ]
                                        ]
                                    ]
                            ],
                            [
                                'title'=> 'Calander option',
                                'force_select' => null,
                                'items'=>
                                    [
                                        [
                                            'title' => 'Expiration Date',
                                            'type' => 'expiration date'   
                                        ],
                                        [
                                            'title' => 'Strike',
                                            'type' => 'double' 

                                        ],
                                        [
                                            'title' => 'Quantity',
                                            'type' => 'double'   
                                        ],
                                    ],
                             "trade_confirmation_group"=>[
                                        "options" =>
                                     [
                                        "items" => [
                                            [
                                                "title"=>"is_offer",
                                                "type"=>"boolean"
                                            ],
                                            [
                                                "title"=>"Put",
                                                "type"=>"double"
                                            ],
                                            [
                                                'title' =>'Call',
                                                'type'  =>'double'
                                            ],
                                            [
                                                "title"=>"Volatility",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Gross Premiums",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Net Premiums" ,
                                                "type"=>"double"
                                            ],
                                            [
                                            "title"=>"Contract",
                                            "type"=> "double"
                                            ]
                                        ]
                                    ],
                                        "futures" =>
                                        [
                                            "items" => [
                                                [
                                                    "title"=>"is_offer",
                                                    "type"=> "boolean"
                                                ],
                                                [
                                                    "title"=>"Future",
                                                    "type"=> "double"
                                                ],
                                                [
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
            'id' => 4,
            'title' => 'Fly',
            'is_selectable' => true,
                'trade_structure_group' => [
                    [
                        'title'=> 'default',
                        'force_select' => true,
                        'items'=>
                            [
                                [
                                    'title' => 'Expiration Date',
                                    'type' => 'expiration date'   
                                ],
                                [
                                    'title' => 'Strike',
                                    'type' => 'double' 

                                ],
                                [
                                    'title' => 'Quantity',
                                    'type' => 'double'   
                                ],
                            ],
                             "trade_confirmation_group"=>[
                                        "options" =>
                                     [
                                        "items" => [
                                            [
                                                "title"=>"is_offer",
                                                "type"=>"boolean"
                                            ],
                                            [
                                                "title"=>"Put",
                                                "type"=>"double"
                                            ],
                                            [
                                                'title' =>'Call',
                                                'type'  =>'double'
                                            ],
                                            [
                                                "title"=>"Volatility",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Gross Premiums",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Net Premiums" ,
                                                "type"=>"double"
                                            ],
                                            [
                                            "title"=>"Contract",
                                            "type"=> "double"
                                            ]
                                        ]
                                    ],
                                        "futures" =>
                                        [
                                            "items" => [
                                                [
                                                    "title"=>"is_offer",
                                                    "type"=> "boolean"
                                                ],
                                                [
                                                    "title"=>"Future",
                                                    "type"=> "double"
                                                ],
                                                [
                                                    "title"=>"Contract",
                                                    "type"=> "double"
                                                ]
                                            ]
                                        ]
                                    ]
                    ],
                    [
                        'title'=> 'Fly Second option',
                        'force_select' => false,
                        'items'=>
                            [
                                [
                                    'title' => 'Strike',
                                    'type' => 'double' 

                                ],
                                [
                                    'title' => 'Quantity',
                                    'type' => 'double'   
                                ],
                            ],
                             "trade_confirmation_group"=>[
                                        "options" =>
                                     [
                                        "items" => [
                                            [
                                                "title"=>"is_offer",
                                                "type"=>"boolean"
                                            ],
                                            [
                                                "title"=>"Put",
                                                "type"=>"double"
                                            ],
                                            [
                                                'title' =>'Call',
                                                'type'  =>'double'
                                            ],
                                            [
                                                "title"=>"Volatility",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Gross Premiums",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Net Premiums" ,
                                                "type"=>"double"
                                            ],
                                            [
                                            "title"=>"Contract",
                                            "type"=> "double"
                                            ]
                                        ]
                                    ],
                                        "futures" =>
                                        [
                                            "items" => [
                                                [
                                                    "title"=>"is_offer",
                                                    "type"=> "boolean"
                                                ],
                                                [
                                                    "title"=>"Future",
                                                    "type"=> "double"
                                                ],
                                                [
                                                    "title"=>"Contract",
                                                    "type"=> "double"
                                                ]
                                            ]
                                        ]
                                    ]
                    ],
                    [
                        'title'=> 'Fly third option',
                        'force_select' => true,
                        'items'=>
                            [
                                [
                                    'title' => 'Strike',
                                    'type' => 'double' 

                                ],
                                [
                                    'title' => 'Quantity',
                                    'type' => 'double'   
                                ],
                            ],
                             "trade_confirmation_group"=>[
                                        "options" =>
                                     [
                                        "items" => [
                                            [
                                                "title"=>"is_offer",
                                                "type"=>"boolean"
                                            ],
                                            [
                                                "title"=>"Put",
                                                "type"=>"double"
                                            ],
                                            [
                                                'title' =>'Call',
                                                'type'  =>'double'
                                            ],
                                            [
                                                "title"=>"Volatility",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Gross Premiums",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Net Premiums" ,
                                                "type"=>"double"
                                            ],
                                            [
                                            "title"=>"Contract",
                                            "type"=> "double"
                                            ]
                                        ]
                                    ],
                                        "futures" =>
                                        [
                                            "items" => [
                                                [
                                                    "title"=>"is_offer",
                                                    "type"=> "boolean"
                                                ],
                                                [
                                                    "title"=>"Future",
                                                    "type"=> "double"
                                                ],
                                                [
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
            'id' => 5,
            'title' => 'Option Switch',
            'is_selectable' => false,

                'trade_structure_group' => [
                    [
                        'title'=> 'default',
                        'force_select' => null,
                        'items'=>
                            [
                                [
                                    'title' => 'Expiration Date',
                                    'type' => 'expiration date'   
                                ],
                                [
                                    'title' => 'Strike',
                                    'type' => 'double' 

                                ],
                                [
                                    'title' => 'Quantity',
                                    'type' => 'double'   
                                ],
                            ],
                             "trade_confirmation_group"=>[
                                        "options" =>
                                     [
                                        "items" => [
                                            [
                                                "title"=>"is_offer",
                                                "type"=>"boolean"
                                            ],
                                            [
                                                "title"=>"Put",
                                                "type"=>"double"
                                            ],
                                            [
                                                'title' =>'Call',
                                                'type'  =>'double'
                                            ],
                                            [
                                                "title"=>"Volatility",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Gross Premiums",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Net Premiums" ,
                                                "type"=>"double"
                                            ],
                                            [
                                            "title"=>"Contract",
                                            "type"=> "double"
                                            ]
                                        ]
                                    ],
                                        "futures" =>
                                        [
                                            "items" => [
                                                [
                                                    "title"=>"is_offer",
                                                    "type"=> "boolean"
                                                ],
                                                [
                                                    "title"=>"Future",
                                                    "type"=> "double"
                                                ],
                                                [
                                                    "title"=>"Contract",
                                                    "type"=> "double"
                                                ]
                                            ]
                                        ]
                                    ]
                    ],
                    [
                        'title'=> 'Switch Options',
                        'force_select' => null,
                        'items'=>
                            [
                                [
                                    'title' => 'Expiration Date',
                                    'type' => 'expiration date'   
                                ],
                                [
                                    'title' => 'Strike',
                                    'type' => 'double' 

                                ],
                                [
                                    'title' => 'Quantity',
                                    'type' => 'double'   
                                ],
                            ],
                             "trade_confirmation_group"=>[
                                        "options" =>
                                     [
                                        "items" => [
                                            [
                                                "title"=>"is_offer",
                                                "type"=>"boolean"
                                            ],
                                            [
                                                "title"=>"Put",
                                                "type"=>"double"
                                            ],
                                            [
                                                'title' =>'Call',
                                                'type'  =>'double'
                                            ],
                                            [
                                                "title"=>"Volatility",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Gross Premiums",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Net Premiums" ,
                                                "type"=>"double"
                                            ],
                                            [
                                            "title"=>"Contract",
                                            "type"=> "double"
                                            ]
                                        ]
                                    ],
                                        "futures" =>
                                        [
                                            "items" => [
                                                [
                                                    "title"=>"is_offer",
                                                    "type"=> "boolean"
                                                ],
                                                [
                                                    "title"=>"Future",
                                                    "type"=> "double"
                                                ],
                                                [
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
            'id' => 6,
            'title' => 'EFP',
            'is_selectable' => false,
                'trade_structure_group' => [
                    [
                        'title'=> 'default',
                        'force_select' => true,
                        'items'=>
                            [
                                [
                                    'title' => 'Expiration Date',
                                    'type' => 'expiration date'   
                                ]
                            ],
                             "trade_confirmation_group"=>[
                                        "options" =>
                                     [
                                        "items" => [
                                            [
                                                "title"=>"is_offer",
                                                "type"=>"boolean"
                                            ],
                                            [
                                                "title"=>"Put",
                                                "type"=>"double"
                                            ],
                                            [
                                                'title' =>'Call',
                                                'type'  =>'double'
                                            ],
                                            [
                                                "title"=>"Volatility",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Gross Premiums",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Net Premiums" ,
                                                "type"=>"double"
                                            ],
                                            [
                                            "title"=>"Contract",
                                            "type"=> "double"
                                            ]
                                        ]
                                    ],
                                        "futures" =>
                                        [
                                            "items" => [
                                                [
                                                    "title"=>"is_offer",
                                                    "type"=> "boolean"
                                                ],
                                                [
                                                    "title"=>"Future",
                                                    "type"=> "double"
                                                ],
                                                [
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
            'id' => 7,
            'title' => 'Rolls',
            'is_selectable' => false,
                'trade_structure_group' => [
                    [
                        'force_select' => true,
                        'title'=> 'default',
                        'items'=>
                            [
                                [
                                    'title' => 'Expiration Date 1',
                                    'type' => 'expiration date'   
                                ],
                                [
                                    'title' => 'Expiration Date 2',
                                    'type' => 'expiration date'   
                                ]
                            ],
                                "trade_confirmation_group"=>[
                                    "options" =>
                                     [
                                        "items" => [
                                            [
                                                "title"=>"is_offer",
                                                "type"=>"boolean"
                                            ],
                                            [
                                                "title"=>"Put",
                                                "type"=>"double"
                                            ],
                                            [
                                                'title' =>'Call',
                                                'type'  =>'double'
                                            ],
                                            [
                                                "title"=>"Volatility",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Gross Premiums",
                                                "type"=>"double"
                                            ],
                                            [
                                                "title"=>"Net Premiums" ,
                                                "type"=>"double"
                                            ],
                                            [
                                            "title"=>"Contract",
                                            "type"=> "double"
                                            ]
                                        ]
                                    ],
                                        "futures" =>
                                        [
                                            "items" => [
                                                [
                                                    "title"=>"is_offer",
                                                    "type"=> "boolean"
                                                ],
                                                [
                                                    "title"=>"Future",
                                                    "type"=> "double"
                                                ],
                                                [
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
            'id' => 8,
            'title' => 'EFP Switch',
            'is_selectable' => false,
                'trade_structure_group' => [
                    [
                        'title'=> 'default',
                        'force_select' => null,
                        'items'=>
                            [
                                [
                                    'title' => 'Expiration Date',
                                    'type' => 'expiration date'   
                                ]
                            ],
                            "trade_confirmation_group"=>[
                            "options" =>
                            [
                                "items" =>
                                [
                                    [
                                        "title"=>"is_offer",
                                        "type"=>"boolean"
                                    ],
                                    [
                                        "title"=>"Put/Call",
                                        "type"=>"boolean"
                                    ],
                                    [
                                        "title"=>"Volatility",
                                        "type"=>"double"
                                    ],
                                    [
                                        "title"=>"Gross Premiums",
                                        "type"=>"double"
                                    ],
                                    [
                                        "title"=>"Net Premiums" ,
                                        "type"=>"double"
                                    ],
                                ]
                            ],
                            "futures" =>
                            [
                                [
                                    "title"=>"is_offer",
                                    "type"=> "boolean"
                                ],
                                [
                                    "title"=>"Future",
                                    "type"=> "double"
                                ],
                                [
                                    "title"=>"Contract",
                                    "type"=> "double"
                                ]
                            ]
                        ]
                    ],
                    [
                        'title'=> 'Switch Options',
                        'force_select' => null,
                        'items'=>
                            [
                                [
                                    'title' => 'Expiration Date',
                                    'type' => 'expiration date'   
                                ]
                            ],
                            "trade_confirmation_group"=>[
                            "options" =>
                            [
                                "items" =>
                                [
                                    [
                                        "title"=>"is_offer",
                                        "type"=>"boolean"
                                    ],
                                    [
                                        "title"=>"Put/Call",
                                        "type"=>"boolean"
                                    ],
                                    [
                                        "title"=>"Volatility",
                                        "type"=>"double"
                                    ],
                                    [
                                        "title"=>"Gross Premiums",
                                        "type"=>"double"
                                    ],
                                    [
                                        "title"=>"Net Premiums" ,
                                        "type"=>"double"
                                    ],
                                ]
                            ],
                            "futures" =>
                            [
                                [
                                    "title"=>"is_offer",
                                    "type"=> "boolean"
                                ],
                                [
                                    "title"=>"Future",
                                    "type"=> "double"
                                ],
                                [
                                    "title"=>"Contract",
                                    "type"=> "double"
                                ]
                            ]
                        ]

                    ]
                ],
        
        ]
    ];

