<?php 

return [
        [
            'id' => 1,
            'title' => 'Outright',
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
                                        'type' => 'Double' 

                                    ],
                                    [
                                        'title' => 'Quantity',
                                        'type' => 'Double'   
                                    ],
                                ]
                        ]
                    ],
        ],
        [
            'id' => 2,
            'title' => 'Risky',
                     'trade_structure_group' =>
                        [
                            [
                                'title'=> 'default',
                                'force_select' => false,
                                'items'=>
                                    [
                                        [
                                            'title' => 'Expiration Date',
                                            'type' => 'expiration date'   
                                        ],
                                        [
                                            'title' => 'Strike',
                                            'type' => 'Double' 

                                        ],
                                        [
                                            'title' => 'Quantity',
                                            'type' => 'Double'   
                                        ],
                                    ]
                            ],
                            [
                                'title'=> 'Risky options',
                                'force_select' => false,
                                'items'=>
                                    [
                                        [
                                            'title' => 'Expiration Date',
                                            'type' => 'expiration date'   
                                        ],
                                        [
                                            'title' => 'Strike',
                                            'type' => 'Double' 

                                        ],
                                        [
                                            'title' => 'Quantity',
                                            'type' => 'Double'   
                                        ],
                                    ]
                            ]
                        ],
        ],
        [
            'id' => 3,
            'title' => 'Calendar',
              'trade_structure_group' =>
                        [
                            [
                                'title'=> 'default',
                                'force_select' => false,
                                'items'=>
                                    [
                                        [
                                            'title' => 'Expiration Date',
                                            'type' => 'expiration date'   
                                        ],
                                        [
                                            'title' => 'Strike',
                                            'type' => 'Double' 

                                        ],
                                        [
                                            'title' => 'Quantity',
                                            'type' => 'Double'   
                                        ],
                                    ]
                            ],
                            [
                                'title'=> 'Calander option',
                                'force_select' => false,
                                'items'=>
                                    [
                                        [
                                            'title' => 'Expiration Date',
                                            'type' => 'expiration date'   
                                        ],
                                        [
                                            'title' => 'Strike',
                                            'type' => 'Double' 

                                        ],
                                        [
                                            'title' => 'Quantity',
                                            'type' => 'Double'   
                                        ],
                                    ]
                            ]
                        ],
        
        ],
        [
            'id' => 4,
            'title' => 'Fly',
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
                                    'type' => 'Double' 

                                ],
                                [
                                    'title' => 'Quantity',
                                    'type' => 'Double'   
                                ],
                            ]
                    ],
                    [
                        'title'=> 'Fly Second option',
                        'force_select' => false,
                        'items'=>
                            [
                                [
                                    'title' => 'Strike',
                                    'type' => 'Double' 

                                ],
                                [
                                    'title' => 'Quantity',
                                    'type' => 'Double'   
                                ],
                            ]
                    ],
                    [
                        'title'=> 'Fly third option',
                        'force_select' => true,
                        'items'=>
                            [
                                [
                                    'title' => 'Strike',
                                    'type' => 'Double' 

                                ],
                                [
                                    'title' => 'Quantity',
                                    'type' => 'Double'   
                                ],
                            ]
                    ]
                ],
        
        ],
        [
            'id' => 5,
            'title' => 'Option Switch',
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
                                    'type' => 'Double' 

                                ],
                                [
                                    'title' => 'Quantity',
                                    'type' => 'Double'   
                                ],
                            ]
                    ],
                    [
                        'title'=> 'Switch Options',
                        'force_select' => true,
                        'items'=>
                            [
                                [
                                    'title' => 'Expiration Date',
                                    'type' => 'expiration date'   
                                ],
                                [
                                    'title' => 'Strike',
                                    'type' => 'Double' 

                                ],
                                [
                                    'title' => 'Quantity',
                                    'type' => 'Double'   
                                ],
                            ]
                    ]
                ],
        
        ],
        [
            'id' => 6,
            'title' => 'EFP',
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
                            ]
                    ]
                ],
        
        ],
        [
            'id' => 7,
            'title' => 'Rolls',
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
                            ]
                    ]
                ],
        
        ],
        [
            'id' => 8,
            'title' => 'EFP Switch',
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
                            ]
                    ]
                ],
        
        ]
    ];

