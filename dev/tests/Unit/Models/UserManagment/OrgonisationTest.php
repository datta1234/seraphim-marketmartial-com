<?php

namespace Tests\Unit\Models\UserManagment;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrgonisationTest extends TestCase
{
    
    use RefreshDatabase;

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
    	$slack_Intergrations	= factory( \App\Models\ApiIntegration\SlackIntegration::class )->create();

    	$this->assertDatabaseMissing('organisation_slack_intergration', [
    		'organisation_id' => $organisation->id,
    		'slack_integration_id' => $slack_Intergrations->id,
    	]);

    	$organisation->slackIntegrations()->attach( $slack_Intergrations );
    	$organisation->slackIntegrations()->sync( $slack_Intergrations );
    	$fetch_organisation = \App\Models\UserManagement\Organisation::find(  $organisation->id )->load('slackIntegrations');

    	// Dont know what i have broken here .......

    	$this->assertDatabaseHas('organisation_slack_intergration', [
    		'organisation_id' => $fetch_organisation->id,
    		'slack_integration_id' => $fetch_organisation->slackIntegrations->id,
    	]);

    	$this->assertDatabaseHas('organisations', [
	    		'id'		=> $fetch_organisation->id,
	            'title'		=> $fetch_organisation->title,
	            'verified'	=> $fetch_organisation->verified,
	            'description'=> $fetch_organisation->description
		]);

		$this->assertDatabaseHas('slack_integrations', [
	    		'id'		=> $fetch_organisation->slackIntegrations->id,
	            'type'		=> $fetch_organisation->slackIntegrations->type,
	            'field'		=> $fetch_organisation->slackIntegrations->field,
	            'value'		=> $fetch_organisation->slackIntegrations->value
		]);


		$this->assertNull( 
			\Validator::make($fetch_organisation->toArray(), [
				'created_at' => 'date_format:Y-m-d H:i:s',
				'updated_at' => 'date_format:Y-m-d H:i:s'
			])->validate()
		);

		$this->assertNull( 
			\Validator::make($fetch_organisation->slackIntegrations->toArray(), [
				'created_at' => 'date_format:Y-m-d H:i:s',
				'updated_at' => 'date_format:Y-m-d H:i:s'
			])->validate()
		);


    }


}
