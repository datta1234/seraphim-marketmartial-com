<?php

namespace Tests\Unit\Models\UserManagment;

use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

/**
 *
 * Testes are grouped buy relations and data
 *
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
     * @group data
     *
     * @uses   \App\Models\UserManagement\Role
     * @uses   \App\Models\UserManagement\Organisation
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
     * @group data
     *
	 * @uses   \App\Models\UserManagement\Role
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
     * @group relations/usermanagment
     *
     * @uses   \App\Models\UserManagement\Role
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
     * @group relations/usermanagment
     *
	 * @uses   \App\Models\UserManagement\Role
     * @uses   \App\Models\UserManagement\Organisation
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

     /**
     * User belongs to many Interest relation Test
     *
     * @covers \App\Models\UserManagement\User::interests
     * @testdox User belongs to many\App\Models\UserManagement\Interest test
     * @group relations/usermanagment
     *
	 * @uses   \App\Models\UserManagement\Role
     * @uses   \App\Models\UserManagement\Interest

     * @return void
     */
    public function testUserBelongsToManyInterests()
    {	

    	factory(\App\Models\UserManagement\Role::class)->create();

    	$faker 			= Faker::create();
    	$filler 		= $faker->word;
    	$user 			= factory( \App\Models\UserManagement\User::class )->create();
    	$interests 		= factory( \App\Models\UserManagement\Interest::class, 5 )->create();

    	foreach ($interests as $interest ) {
    		
    		$this->assertDatabaseMissing('interest_user', [
            	'user_id'          	=> $user->id,
            	'interest_id' 		=> $interest->id,
            	'value'				=> $filler
        	]);

    	}
    	
    	$user->interests()->attach( $interests, ['value' => $filler] );
    	$user->interests()->sync( $interests );
    	$user->load('interests');
    	
    	foreach ($user->interests as $interest ) {

    		$this->assertInternalType('string', $interest->pivot->value);

    		$this->assertDatabaseHas('interest_user', [
            	'user_id'          	=> $user->id,
            	'interest_id' 		=> $interest->id,
            	'value'				=> $filler
        	]);

        	$this->assertDatabaseHas('interests', [
            	'id'          		=> $interest->id,
            	'title' 			=> $interest->title
        	]);

    	}

    	
    }
    
     /**
     * User emails has many email relation Test
     *
     * @covers \App\Models\UserManagement\User::emails
     * @testdox User belongs to many\App\Models\UserManagement\Email test
     * @group relations/usermanagment
     *
	 * @uses   \App\Models\UserManagement\Role
     * @uses   \App\Models\UserManagement\Email

     * @return void
     */
    public function testUserHasManyEmails()
    {	

    	factory(\App\Models\UserManagement\Role::class)->create();

    	$faker 			= Faker::create();


    	$user 			= factory( \App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('emails', [
            	'user_id' => $user->id
        	]);

    	$emails = factory( \App\Models\UserManagement\Email::class, 5 )->create([ 'user_id' => $user->id ]);

    	foreach ($emails as $email ) {
    		
    		$this->assertDatabaseHas('emails', [
    			'id'		=> $email->id,
            	'user_id'	=> $user->id,
            	'title'		=> $email->title,
            	'email'		=> $email->email,
            	'default_id'=> $email->default_id,
            	'notifiable'=> $email->notifiable
        	]);

    	}

    }

    /**
     * User emails has many userOtp relation Test
     *
     * @covers \App\Models\UserManagement\User::userOtps
     * @testdox User belongs to many\App\Models\UserManagement\UserOtps test
     * @group relations/usermanagment
     *
	 * @uses   \App\Models\UserManagement\Role
     * @uses   \App\Models\UserManagement\UserOtps

     * @return void
     */
    public function testUserHasManyUserOtp()
    {	

    	factory(\App\Models\UserManagement\Role::class)->create();

    	$faker 			= Faker::create();
    	$now 			= Carbon::now()->toDateTimeString();
    	$user 			= factory( \App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('user_otps', [ 'user_id' => $user->id ]);

    	$user_otps		= factory( \App\Models\UserManagement\UserOtp::class, 5 )->create([ 'user_id' => $user->id, 'expires_at' => $now ]);

    	$user->load('userOtps')->each(function ($item, $key) use ($user_otps, $user) {

    		$this->assertDatabaseHas('user_otps', [

    			'id'		=> $user_otps[$key]->id,
    			'otp'		=> $user_otps[$key]->otp,
    			'user_id'	=> $user->id,
    			'expires_at'=> $user_otps[$key]->expires_at,
            	
        	]);

		});

    }

     /**
     * User emails has many UserNotification relation Test
     *
     * @covers \App\Models\UserManagement\User::userNotifications
     * @testdox User has many \App\Models\UserManagement\userNotifications test
     * @group relations/usermanagment
     *
	 * @uses   \App\Models\UserManagement\Role
     * @uses   \App\Models\UserManagement\userNotifications

     * @return void
     */
    public function testUserHasManyUserNotification()
    {	

    	factory(\App\Models\UserManagement\Role::class)->create();

    	$faker 			= Faker::create();
    	$now 			= Carbon::now()->toDateString();
    	$user 			= factory( \App\Models\UserManagement\User::class )->create();

	}
}
