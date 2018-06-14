<?php

namespace Tests\Unit\Models\UserManagment;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @coversDefaultClass \App\UserManagement\User
 * @runInSeparateProcess
 */
class UserTest extends TestCase
{ 

	use RefreshDatabase;


    /**
     * User Model Type Casting test
     *
     * Not accounting for null values within the casting test.
     * @coversNothing
     * @testdox check App\Models\UserManagement\User::class casting
     *
     * @return void
     */
    public function testAttributeCasting()
    {

    	factory(\App\Models\UserManagement\Role::class)->create();
    	$organisation 	= factory( \App\Models\UserManagement\Organisation::class)->create();
    	$user 			= factory( \App\Models\UserManagement\User::class )->create([
                        	'organisation_id' =>  $organisation->id
                   		]);
    	
    	$user_fetch		= \App\Models\UserManagement\User::find($user->id);

    	$this->assertInternalType('int', $user_fetch->id);
    	$this->assertInternalType('int', $user_fetch->role_id);
    	$this->assertInternalType('int', $user_fetch->organisation_id);

    	$this->assertInternalType('bool', $user_fetch->active);
    	$this->assertInternalType('bool', $user_fetch->tc_accepted);
    	$this->assertInternalType('bool', $user_fetch->is_married);
    	$this->assertInternalType('bool', $user_fetch->has_children);

    	$this->assertInternalType('string', $user_fetch->email);
    	$this->assertInternalType('string', $user_fetch->full_name);
    	$this->assertInternalType('string', $user_fetch->cell_phone);
    	$this->assertInternalType('string', $user_fetch->work_phone);

    	$this->assertInternalType('string', $user_fetch->hobbies);

    }

    /**
     * Test Hidden model fields are not presant
     *
     * @coversNothing
     * @testdox Test that $hidden Model attributes are not presant
     *
     * @return void
     */
    public function testHiddenAttributes()
    {

    	factory(\App\Models\UserManagement\Role::class)->create();
    	$user 		= factory( \App\Models\UserManagement\User::class )->create();
    	$fetch 		= \App\Models\UserManagement\User::find($user->id);

    	$this->assertNotContains('string', $fetch->password );
		$this->assertNotContains('string', $fetch->remember_token );

    }

     /**
     * User Role relation Test
     *
     * @covers \App\Models\UserManagement\User::role
     * @testdox User belongs to \App\Models\UserManagement\Role test
     *
     * @return void
     */
    public function testUserBelongsToRole()
    {

    	factory(\App\Models\UserManagement\Role::class)->create();
    	$user 			= factory( \App\Models\UserManagement\User::class )->create();
    	$user_role_id 	= $user->id;

    	$this->assertDatabaseHas('users', [
    		'id'		=> $user->id,
            'role_id'	=> $user_role_id
        ]);


    }

     /**
     * User Orgnisation relation Test
     *
     * @covers \App\Models\UserManagement\User::organisation
     * @testdox User belongs to \App\Models\UserManagement\Orgonisation test
     *
     * @return void
     */
    public function testUserBelongsToOrganisation()
    {	

    	factory(\App\Models\UserManagement\Role::class)->create();
    	$organisation 	= factory( \App\Models\UserManagement\Organisation::class)->create();
    	$user 			= factory( \App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('users', [
            'id'          		=> $user->id,
            'organisation_id' 	=> $organisation->id,
        ]);

        $user->organisation()->associate( $organisation )->save();

        $this->assertDatabaseHas('users', [
			'id'          		=> $user->id,
            'organisation_id' 	=> $organisation->id,
        ]);

    }

}
