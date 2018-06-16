<?php

namespace Tests\Unit\Models\UserManagment;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Email Model Type Casting test
     *
     * Not accounting for null values within the casting test.
     * @coversNothing
     * @group data
 	 *
     * @return void
     */
    public function testEmailCasting()
    {

    	$user 		= factory( \App\Models\UserManagement\User::class)->create();
    	$label 		= factory(\App\Models\UserManagement\DefaultLabel::class)->create();
    	$email 		= factory( \App\Models\UserManagement\Email::class )->create(['user_id'=>$user->id, 'default_id'=>$label->id]);
    	$fetch_email = \App\Models\UserManagement\Email::find( $email->id );

		$this->assertInternalType('int', $fetch_email->id);
		$this->assertInternalType('int', $fetch_email->user_id);
		$this->assertInternalType('string', $fetch_email->title);
		$this->assertInternalType('string', $fetch_email->email);
    	$this->assertInternalType('int', $fetch_email->default_id);
    	$this->assertInternalType('int', $fetch_email->notifiable);

		$this->assertNull( 
    		\Validator::make($fetch_email->toArray(), [
				'created_at' => 'date_format:Y-m-d H:i:s',
				'updated_at' => 'date_format:Y-m-d H:i:s'
			])->validate()
    	);

    }


    /**
     * Email belongs to many Default label relation test
     *
     * @covers \App\Models\UserManagement\Email::defaultLabel
     * @group relations/usermanagment
     *	 
     * @uses \App\Models\UserManagement\User
     * @uses \App\Models\UserManagement\DefaultLabel
     *
     * @return void
     */
    public function testEmailBelongsToManylabel()
    {

    	$label = factory(\App\Models\UserManagement\DefaultLabel::class)->create();
    	$user = factory( \App\Models\UserManagement\User::class)->create();

    	$this->assertDatabaseMissing( 'emails', [
    		'default_id' => $label->id
    	]);

    	$emails = factory(\App\Models\UserManagement\Email::class, 5)->create([
    		'default_id' => $label->id,
    		'user_id'=>$user->id
    	])->keyBy('id');

    	$emails->keyBy('id')->each( function($email,$key) use ($emails, $label){

    		$email_label = $email->defaultLabel;

    		$this->assertEquals( $email_label->toArray(), $label->toArray());
	    	$this->assertArrayHasKey('title', $email_label->toArray());
    		$this->assertDatabaseHas('default_label', [
				'title' => $email_label->title,

			]);
			$this->assertNull( 
	    		\Validator::make($email_label->toArray(), [
					'created_at' => 'date_format:Y-m-d H:i:s',
					'updated_at' => 'date_format:Y-m-d H:i:s'
				])->validate()
	    	);

			$this->assertEquals( $email->toArray(), $emails[$key]->toArray());

	    	$this->assertArrayHasKey('user_id', $email->toArray());
	    	$this->assertArrayHasKey('title', $email->toArray());
	    	$this->assertArrayHasKey('email', $email->toArray());
	    	$this->assertArrayHasKey('default_id', $email->toArray());
	    	$this->assertArrayHasKey('notifiable', $email->toArray());
    		$this->assertDatabaseHas('emails', [
    			'user_id' => $email->user_id,
				'title' => $email->title,
				'email' => $email->email,
				'default_id' => $email_label->id,
				'title' => $email->title,
				'notifiable' => $email->notifiable
			]);
	    	$this->assertNull( 
	    		\Validator::make($email->toArray(), [
					'created_at' => 'date_format:Y-m-d H:i:s',
					'updated_at' => 'date_format:Y-m-d H:i:s'
				])->validate()
	    	);

    	});

    }


     /**
     * Email belongs to many users relation test
     *
     * @covers \App\Models\UserManagement\Email::users
     * @group relations/usermanagment
     *	 
     * @uses \App\Models\UserManagement\User
     * @uses \App\Models\UserManagement\DefaultLabel
     *
     * @return void
     */
    public function testEmailBelongsToManyUsers()
    {

    	$label = factory(\App\Models\UserManagement\DefaultLabel::class)->create();
    	$user = factory( \App\Models\UserManagement\User::class)->create();

    	$this->assertDatabaseMissing( 'emails', [
    		'user_id' => $user->id
    	]);

    	$emails = factory(\App\Models\UserManagement\Email::class, 5)->create([
    		'default_id' => $label->id,
    		'user_id'=>$user->id
    	])->keyBy('id');

    	$emails->keyBy('id')->each( function($email,$key) use ($emails, $user){

    		$email_user = $email->user;

    		$this->assertEquals( $email_user->toArray(), $user->toArray());
	    	$this->assertArrayHasKey('full_name', $email_user->toArray());
	    	$this->assertArrayHasKey('cell_phone', $email_user->toArray());
	    	$this->assertArrayHasKey('work_phone', $email_user->toArray());
	    	$this->assertArrayHasKey('role_id', $email_user->toArray());
	    	$this->assertArrayHasKey('organisation_id', $email_user->toArray());
	    	$this->assertArrayHasKey('active', $email_user->toArray());
	    	$this->assertArrayHasKey('tc_accepted', $email_user->toArray());
	    	$this->assertArrayHasKey('birthdate', $email_user->toArray());
	    	$this->assertArrayHasKey('is_married', $email_user->toArray());
	    	$this->assertArrayHasKey('has_children', $email_user->toArray());
	    	$this->assertArrayHasKey('hobbies', $email_user->toArray());
	    	$this->assertNotContains('password', $email_user->password );
			$this->assertNotContains('remember_token', $email_user->remember_token );
			$this->assertNotContains('last_login', $email_user->remember_token );
   			$this->assertDatabaseHas('users', [
				'full_name' => $email_user->full_name,
				'cell_phone' => $email_user->cell_phone,
				'work_phone' => $email_user->work_phone,
				'role_id' => $email_user->role_id,
				'organisation_id' => $email_user->organisation_id,
				'active' => $email_user->active,
				'tc_accepted' => $email_user->tc_accepted,
				'birthdate' => $email_user->birthdate,
				'is_married' => $email_user->is_married,
				'has_children' => $email_user->has_children,
				'hobbies' => $email_user->hobbies,
			]);
   			$this->assertNull( 
	    		\Validator::make($email_user->toArray(), [
					'created_at' => 'date_format:Y-m-d H:i:s',
					'updated_at' => 'date_format:Y-m-d H:i:s'
				])->validate()
	    	);

			$this->assertEquals( $email->toArray(), $emails[$key]->toArray());
	    	$this->assertArrayHasKey('user_id', $email->toArray());
	    	$this->assertArrayHasKey('title', $email->toArray());
	    	$this->assertArrayHasKey('email', $email->toArray());
	    	$this->assertArrayHasKey('default_id', $email->toArray());
	    	$this->assertArrayHasKey('notifiable', $email->toArray());
    		$this->assertDatabaseHas('emails', [
    			'user_id' => $email->user_id,
				'title' => $email->title,
				'email' => $email->email,
				'default_id' => $email->default_id,
				'title' => $email->title,
				'notifiable' => $email->notifiable
			]);
	    	$this->assertNull( 
	    		\Validator::make($email->toArray(), [
					'created_at' => 'date_format:Y-m-d H:i:s',
					'updated_at' => 'date_format:Y-m-d H:i:s'
				])->validate()
	    	);

    	});

    }

    /*
public function getLabelAttribute()
    {
        return $this->defaultLabel()->exists() ? $this->defaultLabel->title : $this->title ; 
    }
    */

     /**
     * Email Get label Attribute test
     *
     * @covers \App\Models\UserManagement\Email::getLabelAttribute
     * @group attributes/usermanagment
     *	 
     * @uses \App\Models\UserManagement\User
     * @uses \App\Models\UserManagement\DefaultLabel
     *
     * @return void
     */
    public function testEmailGetDefaultLabelAttribute()
    {

    	$label = factory(\App\Models\UserManagement\DefaultLabel::class)->create();
    	$user = factory( \App\Models\UserManagement\User::class)->create();
    	$email = factory(\App\Models\UserManagement\Email::class)->create([
    		'default_id' => $label->id,
    		'user_id'=>$user->id
    	]);
    	$fetch_emails = \App\Models\UserManagement\Email::find( $email->id );

    	$this->assertEquals($fetch_emails->title, $label->title);

    }


}
