<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class UserMarketRequestTest extends TestCase
{
    /**
     * Test creating a market
     *
     * @return void
     */
    public function testStoreIndexOutrightUsermarketRequest()
    {
		$organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
		$user = factory(\App\Models\UserManagement\User::class)->create([
			'organisation_id' => $organisation->id
		]);
		$safexDate = \App\Models\StructureItems\SafexExpirationDate::where('expiration_date','>',Carbon::now())->first();
		$market =  \App\Models\StructureItems\Market::where('title',array_random(['TOP40','CTOP','CTOR']))->first();
		$userMarketRequest = [
			"trade_structure"=> "Outright",
			"trade_structure_groups"=> [
				[
					"is_selected"=> 1,
					"market_id"=> $market->id,
					"fields"=> [
					   "Expiration Date"=> $safexDate->expiration_date->format("Y-m-d"),
					   "Strike"=> rand(0,100),
					   "Quantity"=> rand(0,1000)
						]
				]
			]
		];

	    $response = $this->actingAs($user)->json('POST',"trade/market/{$market->id}/market-request", $userMarketRequest);
	    $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ["trade_items"=>
										["default"=>
											[
												"market" 			=> $market->title,
												"Expiration Date" 	=> $userMarketRequest['trade_structure_groups'][0]['fields']['Expiration Date'],
												"Strike" 			=> $userMarketRequest['trade_structure_groups'][0]['fields']['Strike'],
												"Quantity"			=> $userMarketRequest['trade_structure_groups'][0]['fields']['Quantity'],
											]
										]
							],
				"message"=>"Market Request created successfully."
			]);
    }

    /**
     * Test validation using wrong 
     *
     * @return void
     */
    public function testValidationErrorsindexOutrightUsermarketRequest()
    {
		$organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
		$user = factory(\App\Models\UserManagement\User::class)->create([
			'organisation_id' => $organisation->id
		]);
		$safexDate = \App\Models\StructureItems\SafexExpirationDate::where('expiration_date','<',Carbon::now())->first();//old date
		$market =  \App\Models\StructureItems\Market::where('title',array_random(['TOP40','CTOP','CTOR']))->first();
		$userMarketRequest = [
			"trade_structure"=> "Outright",
			"trade_structure_groups"=> [
				[
					"is_selected"	=> 0,
					"market_id"		=> $market->id,
					"fields"		=> [
					   					"Expiration Date"	=> $safexDate->expiration_date->format("Y-m-d"),
					   					"Strike"			=> "String",
					   					"Quantity"			=> "String"
									]
				]
			]
		];

	    $response = $this->actingAs($user)->json('POST',"trade/market/{$market->id}/market-request", $userMarketRequest);

	    $response
            ->assertStatus(422)
            ->assertJson([
                'errors'  => [
					'trade_structure_groups.0.fields.Expiration Date'	=> ['Please select a date that is after today.'],
					'trade_structure_groups.0.fields.Strike'			=> ['Please ensure that you enter a valid amount.'],
					'trade_structure_groups.0.fields.Quantity'			=> ['Please ensure that you enter a valid amount.']
                ],
				'message' => 'The given data was invalid.'
			]);

    }

    public function testRequiredErrorsindexOutrightUsermarketRequest()
    {
		$organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
		$user = factory(\App\Models\UserManagement\User::class)->create([
			'organisation_id' => $organisation->id
		]);
		$market =  \App\Models\StructureItems\Market::where('title',array_random(['TOP40','CTOP','CTOR']))->first();
		$userMarketRequest = [
			"trade_structure"					=> "Outright",
			"trade_structure_groups"			=> [
				[
					"is_selected"				=> 0,
					"market_id"					=> "",
					"fields"					=> [
						   "Expiration Date"	=> "",
						   "Strike"				=> "",
						   "Quantity"			=> ""
						]
				]
			]
		];

	    $response = $this->actingAs($user)->json('POST',"trade/market/{$market->id}/market-request", $userMarketRequest);
	    $response
            ->assertStatus(422)
            ->assertJson([
                'errors'  => [
					'trade_structure_groups.0.fields.Expiration Date'=>[
						'Field is required.'
					],
					'trade_structure_groups.0.fields.Strike'=>[
						'Field is required.'
					],
					'trade_structure_groups.0.fields.Quantity'=>[
						'Field is required.'
					]
                ],
				'message' => 'The given data was invalid.'
			]);

    }
}
