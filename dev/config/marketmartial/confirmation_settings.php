<?php
return [
	"futures" => [
		"index" => [
			"all_futures"=> 0.123
		],
		"singles" => [
			"all_futures"=> 0.456
		],
	],
  	// trade_structure fees are percentage values
  	// __trade_structure__
	"outright"=>
	[
		"index" => 
		[
			"only_leg"=>0.003
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
			"small_leg"=>0.002,
			"big_leg"=>0.002
		],
		"singles"=>
		[
			"small_leg"=>0.035,
			"big_leg"=>0.035
		]
	],

	// __trade_structure__
	"calendar"=>
	[
		"index" => 
		[
			"small_leg"=>0.003,
			"big_leg"=>0.003

		],
		"singles"=>
		[
			"small_leg"=>0.035,
			"big_leg"=>0.035

		]
	],
	// __trade_structure__
	"fly"=>
	[
		"index" => 
		[
			"per_leg"=>0.002
		],
		"singles"=>
		[
			"per_leg"=>0.03
		]
	],
	// __trade_structure__
	"option_switch"=>
	[
		"index" => 
		[
			"per_leg"=>0.003
		],
		"singles"=>
		[
			"per_leg"=>0.05
		]
	],
	// __trade_structure__
	"efp"=>
	[
		"index" => 
		[
			"only_leg"=>0.002
		],
	],
	// __trade_structure__
	"rolls"=>
	[
		"index" => 
		[
			"far_leg_only"=>0.002
		],
	],
	// __trade_structure__
	"efp_switch"=>
	[
		"index" => 
		[
			"per_leg"=>0.002
		],
	],
];