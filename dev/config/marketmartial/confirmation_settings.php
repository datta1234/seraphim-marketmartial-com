<?php
return [
	//big leg is based of the nominal
    'rebate_percentage'   =>  env('REBATE_PERCENTAGE',0.25),
  // __trade_structure__
	"outright"=>
	[
		"index" => 
		[
			"only_leg"=>0.004
		],
		"singles"=>
		[
			"only_leg"=>0.05
		]
	],
	// __trade_structure__
	"risky"=>
	[
		"index" => 
		[
			"big_leg"=>0.004,
			"small_leg"=>0.002
		],
		"singles"=>
		[
			"big_leg"=>0.05,
			"small_leg"=>0.025
		]
	],

	// __trade_structure__
	"calendar"=>
	[
		"index" => 
		[
			"big_leg"=>0.004,
			"small_leg"=>0.002

		],
		"singles"=>
		[
			"big_leg"=>0.05,
			"small_leg"=>0.05

		]
	],
	// __trade_structure__
	"fly"=>
	[
		"index" => 
		[
			"per_leg"=>0.0025
		],
		"singles"=>
		[
			"per_leg"=>0.035
		]
	],
	// __trade_structure__
	"option_switch"=>
	[
		"index" => 
		[
			"per_leg"=>0.004
		],
		"singles"=>
		[
			"per_leg"=>0.05
		]
	],        
];