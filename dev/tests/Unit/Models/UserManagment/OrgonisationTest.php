<?php

namespace Tests\Unit\Models\UserManagment;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrgonisationTest extends TestCase
{
    
    //use RefreshDatabase;

    /**
     * Orgonisation Model Type Casting test
     *
     * Not accounting for null values within the casting test.
     * @coversNothing
     * @testdox check App\Models\UserManagement\Organisation::class casting
     * @group data
     *
     *	 
     * @return void
     */
    public function testOronisationCasting()
    {

    	$organisation 		= factory( \App\Models\UserManagement\Organisation::class )->create();
    	$fetch_organisation = \App\Models\UserManagement\Organisation::find( $organisation->id );

		$this->assertInternalType('int', $fetch_organisation->id);
		$this->assertInternalType('string', $fetch_organisation->title);
		$this->assertInternalType('bool', $fetch_organisation->verified);
		$this->assertInternalType('string', $fetch_organisation->description);
    	
		$this->assertNull( 
    		\Validator::make($fetch_organisation->toArray(), [
				'created_at' => 'date_format:Y-m-d H:i:s',
				'updated_at' => 'date_format:Y-m-d H:i:s'
			])->validate()
    	);


    }

    /**
     * Organisation has many users relation Test
     *
     * @covers \App\Models\UserManagement\Organisation::users
     * @testdox Organisation has many \App\Models\UserManagement\Organisation test
     * @group relations/usermanagment
     *
     * @uses \App\Models\UserManagement\User
     *
     * @return void
     */
    public function testOrganisationHasManyUsers()
    {

    	$organisation = factory( \App\Models\UserManagement\Organisation::class )->create();

    	$this->assertDatabaseMissing('users', [
			'organisation_id' => $organisation->id
		]);

		$users = factory( \App\Models\UserManagement\User::class, 5 )->create([
			'organisation_id' => $organisation->id
		])->keyBy('id');
		$organisation->load('users');


		$organisation->users->keyBy('id')->each( function($item,$key) use ($organisation, $users) {

			$user = $users[$key];

			$this->assertArrayHasKey('email', $item->toArray());
			$this->assertArrayHasKey('full_name', $item->toArray());
			$this->assertArrayHasKey('cell_phone', $item->toArray());
			$this->assertArrayHasKey('work_phone', $item->toArray());
			$this->assertArrayHasKey('role_id', $item->toArray());
			$this->assertArrayHasKey('organisation_id', $item->toArray());
			$this->assertArrayHasKey('active', $item->toArray());
			$this->assertArrayHasKey('tc_accepted', $item->toArray());
			$this->assertArrayHasKey('birthdate', $item->toArray());
			$this->assertArrayHasKey('is_married', $item->toArray());
			$this->assertArrayHasKey('has_children', $item->toArray());
			$this->assertArrayHasKey('hobbies', $item->toArray());

			$this->assertArrayNotHasKey('remember_token', $item->toArray());
			$this->assertArrayNotHasKey('password', $item->toArray());
			$this->assertArrayNotHasKey('last_login', $item->toArray());

			$this->assertDatabaseHas('organisations', [
	    		'id'		=> $organisation->id,
	            'title'		=> $organisation->title,
	            'verified'	=> $organisation->verified,
	            'description'=> $organisation->description
	        ]);

	        $this->assertDatabaseHas('users', [
	    		'id'		=> $item->id,
	    		'email'		=> $item->email,
	            'full_name'	=> $item->full_name,
	            'cell_phone'	=> $item->cell_phone,
	            'work_phone'	=> $item->work_phone,
	            'role_id'	=> $item->role_id,
	            'organisation_id'	=> $organisation->id,
	            'active'	=> $item->active,
	            'tc_accepted'	=> $item->tc_accepted,
	            'password'	=> $item->password,
	            'remember_token'	=> $item->remember_token,
	            'birthdate'	=> $item->birthdate,
	            'is_married'	=> $item->is_married,
	            'has_children'	=> $item->has_children,
	            'last_login'	=> $item->last_login,
	            'hobbies'	=> $item->hobbies,
	        ]);

			$this->assertNull( 
	    		\Validator::make($item->toArray(), [
					'created_at' => 'date_format:Y-m-d H:i:s',
					'updated_at' => 'date_format:Y-m-d H:i:s'
				])->validate()
	    	);

		});


    }


     /**
     * Organisation belongs to many slack intergrations
     *
     * @covers \App\Models\UserManagement\Organisation::slackIntegrations
     * @testdox Organisation belongs To Many App\Models\ApiIntegration\SlackIntegration test
     * @group relations/ApiIntegration
     *
     * @uses \App\Models\ApiIntegration\SlackIntegration
     *
     * @return void
     */
    public function testOrganisationBelongsToManySlackIntegrations()
    {

    	$organisation 			= factory( \App\Models\UserManagement\Organisation::class )->create();
    	$slackIntegrations	= factory( \App\Models\ApiIntegration\SlackIntegration::class, 5 )->create();

    	foreach ($slackIntegrations as $slack_Intergration ) {

    		$this->assertDatabaseMissing('organisation_slack_intergration', [
            	'organisation_id' => $organisation->id,
    			'slack_integration_id' => $slack_Intergration->id,
        	]);

    	}

		$organisation->slackIntegrations()->attach( $slackIntegrations );
    	$organisation->slackIntegrations()->sync( $slackIntegrations );

    	$fetch_organisation = \App\Models\UserManagement\Organisation::find($organisation->id);
    	$slackIntegrations 	= $slackIntegrations->keyBy('id');

    	$organisation->slackIntegrations->keyBy('id')->each( function($item,$key) use ($fetch_organisation,$slackIntegrations) {

    		$slackIntegration = $slackIntegrations[$key];

    		$this->assertDatabaseHas('organisation_slack_intergration', [
    			'organisation_id' => $fetch_organisation->id,
    			'slack_integration_id' => $slackIntegration->id,
    		]);

			$this->assertArrayHasKey('title', $fetch_organisation->toArray());
			$this->assertArrayHasKey('verified', $fetch_organisation->toArray());
			$this->assertArrayHasKey('description', $fetch_organisation->toArray());
    		$this->assertDatabaseHas('organisations', [
	    		'id'		=> $fetch_organisation->id,
	            'title'		=> $fetch_organisation->title,
	            'verified'	=> $fetch_organisation->verified,
	            'description'=> $fetch_organisation->description
			]);

    		$this->assertNull( 
				\Validator::make($fetch_organisation->toArray(), [
					'created_at' => 'date_format:Y-m-d H:i:s',
					'updated_at' => 'date_format:Y-m-d H:i:s'
				])->validate()
			);

			$this->assertArrayHasKey('type', $slackIntegration->toArray());
			$this->assertArrayHasKey('field', $slackIntegration->toArray());
			$this->assertArrayHasKey('value', $slackIntegration->toArray());
			$this->assertDatabaseHas('slack_integrations', [
	    		'id'		=> $slackIntegration->id,
	            'type'		=> $slackIntegration->type,
	            'field'		=> $slackIntegration->field,
	            'value'		=> $slackIntegration->value
			]);

			$this->assertNull( 
				\Validator::make($slackIntegration->toArray(), [
					'created_at' => 'date_format:Y-m-d H:i:s',
					'updated_at' => 'date_format:Y-m-d H:i:s'
				])->validate()
			);

    	});

    }

   /**
    * Organisation has many Rebates
    *
    * @covers \App\Models\UserManagement\Organisation::rebates
    * @testdox Organisation has Many App\Models\Trade\Rebate test
    * @group relations/Trade
    *
    * @uses \App\Models\Trade\Rebate
    *
    * @return void
    */
    public function testOrganisationHasManyRebates()
    {

    	$organisation = factory(\App\Models\UserManagement\Organisation::class)->create();

    	$this->assertDatabaseMissing('rebates', [
			'organisation_id' => $organisation->id
		]);

    	$rebates = factory( \App\Models\Trade\Rebate::class, 5 )->create([
    		'organisation_id' => $organisation->id
    	])->keyBy('id');

    	$organisation->rebates->keyBy('id')->each( function($item,$key) use ($organisation,$rebates){

    		$rebate = $rebates[$key];

			$this->assertArrayHasKey('user_id', $rebate->toArray());
			$this->assertArrayHasKey('user_market_request_id', $rebate->toArray());
			$this->assertArrayHasKey('user_market_id', $rebate->toArray());
			$this->assertArrayHasKey('organisation_id', $rebate->toArray());
			$this->assertArrayHasKey('is_paid', $rebate->toArray());
			$this->assertArrayHasKey('booked_trade_id', $rebate->toArray());
			$this->assertArrayHasKey('trade_date', $rebate->toArray());
			$this->assertDatabaseHas('rebates', [
				'id' => $rebate->id,
	    		'user_id' => $rebate->user_id,
	    		'user_market_request_id' => $rebate->user_market_request_id,
	    		'user_market_id' => $rebate->user_market_id,
	    		'organisation_id' => $organisation->id,
	    		'booked_trade_id' => $rebate->booked_trade_id,
	    		'trade_date' => $rebate->trade_date,
			]);

			$this->assertNull( 
				\Validator::make($rebate->toArray(), [
					'trade_date' => 'date_format:Y-m-d',
					'created_at' => 'date_format:Y-m-d H:i:s',
					'updated_at' => 'date_format:Y-m-d H:i:s'
				])->validate()
			);

    	});

    }


}
