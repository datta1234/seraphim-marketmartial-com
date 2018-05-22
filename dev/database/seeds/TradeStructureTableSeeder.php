<?php

use Illuminate\Database\Seeder;

class TradeStructureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tradeStructures = [
            [
                'id' => 1,
                'title' => 'Outright',
                'trade_structure_group' =>
                        [
                            [
                                'title'=> 'default',
                                'items'=>
                                    [
                                        [
                                            'title' => 'Expiration Date',
                                            'item_type_id' => 1   
                                        ],
                                        [
                                            'title' => 'Strike',
                                            'item_type_id' => 2 

                                        ],
                                        [
                                            'title' => 'Quantity',
                                            'item_type_id' => 2   
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
                                    'items'=>
                                        [
                                            [
                                                'title' => 'Expiration Date',
                                                'item_type_id' => 1   
                                            ],
                                            [
                                                'title' => 'Strike',
                                                'item_type_id' => 2 

                                            ],
                                            [
                                                'title' => 'Quantity',
                                                'item_type_id' => 2   
                                            ],
                                        ]
                                ],
                                [
                                    'title'=> 'Risky options',
                                    'items'=>
                                        [
                                            [
                                                'title' => 'Strike',
                                                'item_type_id' => 2 

                                            ],
                                            [
                                                'title' => 'Quantity',
                                                'item_type_id' => 2   
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
                                    'items'=>
                                        [
                                            [
                                                'title' => 'Expiration Date',
                                                'item_type_id' => 1   
                                            ],
                                            [
                                                'title' => 'Strike',
                                                'item_type_id' => 2 

                                            ],
                                            [
                                                'title' => 'Quantity',
                                                'item_type_id' => 2   
                                            ],
                                        ]
                                ],
                                [
                                    'title'=> 'Calander option',
                                    'items'=>
                                        [
                                            [
                                                'title' => 'Expiration Date',
                                                'item_type_id' => 1   
                                            ],
                                            [
                                                'title' => 'Strike',
                                                'item_type_id' => 2 

                                            ],
                                            [
                                                'title' => 'Quantity',
                                                'item_type_id' => 2   
                                            ],
                                        ]
                                ]
                            ],
            
            ],
            [
                'id' => 4,
                'title' => 'Fly',
                  'trade_structure_group' =>
                            [
                                [
                                    'title'=> 'default',
                                    'items'=>
                                        [
                                            [
                                                'title' => 'Expiration Date',
                                                'item_type_id' => 1   
                                            ],
                                            [
                                                'title' => 'Strike',
                                                'item_type_id' => 2 

                                            ],
                                            [
                                                'title' => 'Quantity',
                                                'item_type_id' => 2   
                                            ],
                                        ]
                                ],
                                [
                                    'title'=> 'Fly Second option',
                                    'items'=>
                                        [
                                            [
                                                'title' => 'Strike',
                                                'item_type_id' => 2 

                                            ],
                                            [
                                                'title' => 'Quantity',
                                                'item_type_id' => 2   
                                            ],
                                        ]
                                ],
                                [
                                    'title'=> 'Fly third option',
                                    'items'=>
                                        [
                                            [
                                                'title' => 'Strike',
                                                'item_type_id' => 2 

                                            ],
                                            [
                                                'title' => 'Quantity',
                                                'item_type_id' => 2   
                                            ],
                                        ]
                                ]
                            ],
            
            ],
            [
                'id' => 5,
                'title' => 'Fly',
                  'trade_structure_group' =>
                            [
                                [
                                    'title'=> 'default',
                                    'items'=>
                                        [
                                            [
                                                'title' => 'Expiration Date',
                                                'item_type_id' => 1   
                                            ],
                                            [
                                                'title' => 'Strike',
                                                'item_type_id' => 2 

                                            ],
                                            [
                                                'title' => 'Quantity',
                                                'item_type_id' => 2   
                                            ],
                                        ]
                                ],
                                [
                                    'title'=> 'Fly Second option',
                                    'items'=>
                                        [
                                            [
                                                'title' => 'Strike',
                                                'item_type_id' => 2 

                                            ],
                                            [
                                                'title' => 'Quantity',
                                                'item_type_id' => 2   
                                            ],
                                        ]
                                ],
                                [
                                    'title'=> 'Fly third option',
                                    'items'=>
                                        [
                                            [
                                                'title' => 'Strike',
                                                'item_type_id' => 2 

                                            ],
                                            [
                                                'title' => 'Quantity',
                                                'item_type_id' => 2   
                                            ],
                                        ]
                                ]
                            ],
            
            ],
    
    
            // [
            //     'id' => 5,
            //     'title' => 'EFP'
            // ],
            // [
            //     'id' => 6,
            //     'title' => 'Rolls'
            // ]
        ];
        foreach ($tradeStructures as $tradeStructure) 
        {
             App\Models\StructureItems\TradeStructure::saveFullStructure($tradeStructure);
        }
    }
}
