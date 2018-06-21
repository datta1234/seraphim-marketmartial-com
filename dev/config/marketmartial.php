<?php

return [
		"default_role" => "Trader",//pull and use this role for testing as the default role
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
				'is_selectable' => true,
			]
		],
		'market_condition_category' => [
                [
                	'title' => 'Fill or kill'
            	],
            	[
	                'title' => 'Meet in the Middle'
            	],
            	[
	                'title' => 'Buy/Sell at Best'
            	]
		],
	   'market_conditions' => [
            [
                'title' => 'Repeat all the way',
                'alias' => 'Repeat ATW',
                'market_condition_category' => null,
                'timeout' => false
            ],
            [
                'title' => 'Prefer to kill (20min Fok)',
                'alias' => 'FoK: kill',
                'market_condition_category' => 'Fill or kill',
                'timeout' => true
            ],
            [
                'title' => 'Happy to Spin',
                'alias' => 'FoK: fill',
                'market_condition_category' => 'Fill or kill',
                'timeout' => false
            ],
            [
                'title' => 'Propose (Private)',
                'alias' => 'Propose (Private)',
                'market_condition_category' => null,
                'timeout' => false
            ],
            [
                'title' => 'Buy in the middle',
                'alias' => 'Buy at mid(Private)',
                'market_condition_category' => 'Meet in the Middle',
                'timeout' => false

            ],
            [
                'title' => 'Sell in the middle',
                'alias' => 'Sell at mid(Private)',
                'market_condition_category' => 'Meet in the Middle',
                'timeout' => false
            ],
          	[
                'title' => 'Buy at best',
                'alias' => 'Buy at best',
            	'market_condition_category' => 'Buy/Sell at Best',
                'timeout' => true
            ],
            [
                'title' => 'Sell at best',
                'alias' => 'Sell at best',
            	'market_condition_category' => 'Buy/Sell at Best',
                'timeout' => true
            ],
            [
                'title' => 'OCO',
                'alias' => 'OCO',
            	'market_condition_category' => null,
                'timeout' => false
            ],
            [
                'title' => 'Subject',
                'alias' => 'Subject',
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
				'market_type' => 'Index Option',
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
				'market_type' => 'Index Option',
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
				'market_type' => 'Index Option',
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
				'market_type' => 'Single Stock Option',
				'is_selectable' => true
            ],
            [
				'title'=> 'Options Switches',
				'description' => "placed under respective Index market, unless single vs. single, in which case it will be listed under SINGLES",
				'is_seldom' => false,
				'has_deadline'=> true,
				'needs_spot'=>false,
				'has_negotiation'=> true,
				'has_rebate'=> true,
				'market_type' => 'Index Option',
				'is_selectable' => false
            ],
            [
				'title'=> 'EFP',
				'description' => "placed under respective Index market, unless single vs. single, in which case it will be listed under SINGLES",
				'is_seldom' => false,
				'has_deadline'=> true,
				'needs_spot'=>true,
				'has_negotiation'=> true,
				'has_rebate'=> false,
				'market_type' => 'Delta One(EFPs, Rolls and EFP Switches)',
				'is_selectable' => false
            ],
            [
				'title'=> 'Roll',
				'description' => null,
				'is_seldom' => false,
				'has_deadline'=> true,
				'needs_spot'=>false,
				'has_negotiation'=> true,
				'has_rebate'=> false,
				'market_type' => 'Delta One(EFPs, Rolls and EFP Switches)',
				'is_selectable' => true
            ],
             [
				'title'=> 'EFP Switches',
				'description' => "(note: this is different to the Options Switches above, as these are placed under DELTA ONE)",
				'is_seldom' => false,
				'has_deadline'=> true,
				'needs_spot'=>true,
				'has_negotiation'=> true,
				'has_rebate'=> false,
				'market_type' => 'Delta One(EFPs, Rolls and EFP Switches)',
				'is_selectable' => true
            ],
        ]
];