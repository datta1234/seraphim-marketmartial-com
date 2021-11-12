<?php


return [
		"default_role" => "Trader",//pull and use this role for testing as the default role
        "auto_on_hold_minutes" => 20,
        "default_email_labels" => [
            "Direct", // Label that is required at index == 0
            "Group",
            "Clearer",
            "Compliance",
            "Invoices"
        ],
        "AutoSetTradeAccounts" => [
            "Direct"
        ],
		"roles" => [
			[
				'title' => 'Admin',
				'is_selectable' => false,
			],
			[
				'title' => 'Trader',
				'is_selectable' => true,
			],
			[
				'title' => 'Viewer',
				'is_selectable' => false,
			]
		],
		'market_condition_category' => [
                [
                	'title' => 'FoK',
                    'market_condition_category' => null,
            	],
                [
                    'title' => 'Apply To',
                    'market_condition_category' => 'FoK',
                ],
                [
                    'title' => 'Fill or kill',
                    'market_condition_category' => 'FoK',
                ],
            	[
	                'title' => 'Meet in the Middle',
                    'market_condition_category' => null,
            	],
            	[
	                'title' => 'Buy/Sell at Best',
                    'market_condition_category' => null,
            	]
		],
	   'market_conditions' => [
            [
                'title' => 'Repeat all the way',
                'alias' => 'repeat_atw',
                'market_condition_category' => null,
                'timeout' => false
            ],
            [
                'title' => 'My Bid',
                'alias' => 'bid',
                'market_condition_category' => 'Apply To',
                'timeout' => true
            ],
            [
                'title' => 'My Offer',
                'alias' => 'offer',
                'market_condition_category' => 'Apply To',
                'timeout' => false
            ],
            [
                'title' => 'Prefer to kill (20min Fok)',
                'alias' => 'fok_kill',
                'market_condition_category' => 'Fill or kill',
                'timeout' => true
            ],
            [
                'title' => 'Happy to Spin',
                'alias' => 'fok_spin',
                'market_condition_category' => 'Fill or kill',
                'timeout' => false
            ],
            [
                'title' => 'Propose (Private)',
                'alias' => 'propose',
                'market_condition_category' => null,
                'timeout' => false
            ],
            [
                'title' => 'Buy in the middle',
                'alias' => 'buy_at_mid',
                'market_condition_category' => 'Meet in the Middle',
                'timeout' => false

            ],
            [
                'title' => 'Sell in the middle',
                'alias' => 'sell_at_mid',
                'market_condition_category' => 'Meet in the Middle',
                'timeout' => false
            ],
          	[
                'title' => 'Buy at best',
                'alias' => 'buy_at_best',
            	'market_condition_category' => 'Buy/Sell at Best',
                'timeout' => true
            ],
            [
                'title' => 'Sell at best',
                'alias' => 'sell_at_best',
            	'market_condition_category' => 'Buy/Sell at Best',
                'timeout' => true
            ],
            [
                'title' => 'OCO',
                'alias' => 'oco',
            	'market_condition_category' => null,
                'timeout' => false
            ],
            [
                'title' => 'Subject',
                'alias' => 'subject',
            	'market_condition_category' => null,
                'timeout' => false

            ]
        ],
        'market_type' => [
            [
				'title'=> 'Index Option'
            ],
            [
				'title'=> 'Delta One(EFPs, Rolls and EFP Switches)'
            ],
         	[
                'title'=> 'Single Stock Option'
            ]
        ],
        'markets' => [
        [
                'title'=> 'TOP40',
                'description' => null,
                'is_seldom' => false,
                'has_deadline'=> true,
                'needs_spot'=>false,
                'has_negotiation'=> true,
                'has_rebate'=> true,
                'market_type' => "Index Option",
                'parent_id' =>  null,
                'is_displayed'  =>  true,
                'is_selectable' => true
            ],
          	[
                'title'=> 'DTOP',
                'description' => null,
                'is_seldom' => true,
                'has_deadline'=> true,
                'needs_spot'=>false,
                'has_negotiation'=> true,
                'has_rebate'=> true,
                'market_type' => "Index Option",
                'parent_id' =>  null,
                'is_displayed'  =>  true,
                'is_selectable' => true
            ],
         	[
                'title'=> 'DCAP',
                'description' => null,
                'is_seldom' => true,
                'has_deadline'=> true,
                'needs_spot'=>false,
                'has_negotiation'=> true,
                'has_rebate'=> true,
                'market_type' => "Index Option",
                'parent_id' =>  null,
                'is_displayed'  =>  true,
                'is_selectable' => true
            ],
            [
                'title'=> 'SINGLES',
                'description' => null,
                'is_seldom' => false,
                'has_deadline'=> true,
                'needs_spot'=>false,
                'has_negotiation'=> true,
                'has_rebate'=> true,
                'market_type' => "Single Stock Option",
                'parent_id' =>  null,
                'is_displayed'  =>  true,
                'is_selectable' => true
            ],
            [
                'title'=> 'DELTA ONE',
                'description' => "",
                'is_seldom' => false,
                'has_deadline'=> true,
                'needs_spot'=>true,
                'has_negotiation'=> true,
                'has_rebate'=> false,
                'market_type' => "Delta One(EFPs, Rolls and EFP Switches)",
                'parent_id' =>  null,
                'is_displayed'  =>  true,
                'is_selectable' => false
            ]
        ]
];