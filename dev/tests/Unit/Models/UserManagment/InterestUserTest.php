<?php

namespace Tests\Unit\Models\UserManagment;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InterestUserTest extends TestCase
{
    use RefreshDatabase;

    /** 
     * Interest User Model Type Casting test
     *
     * Not accounting for null values within the casting test.
     * @coversNothing
     * @group data
     *
     * @return void
     */
    public function testInterestUserCasting()
    {

    	$user_interest 		= factory( \App\Models\UserManagement\InterestUser::class )->create();
    	$fetch_user_interest = \App\Models\UserManagement\InterestUser::find( $user_interest->id );

		$this->assertInternalType('int', $fetch_user_interest->id);
		$this->assertInternalType('string', $fetch_user_interest->value);
		$this->assertInternalType('int', $fetch_user_interest->interest_id);
		$this->assertInternalType('int', $fetch_user_interest->user_id);
		$this->assertNull( 
    		\Validator::make($fetch_user_interest->toArray(), [
				'created_at' => 'date_format:Y-m-d H:i:s',
				'updated_at' => 'date_format:Y-m-d H:i:s'
			])->validate()
    	);

    }

    /**
     * InterestUser belongs to Interst relation Test
     *
     * @covers \App\Models\UserManagement\InterestUser::interests
     * @group relations/usermanagment
     *
     * @uses \App\Models\UserManagement\Interst
     *
     * @return void
     */
    public function testInterestUSerBelongsToManyInterests()
    {

    	




    }

    /**
     * InterestUser belongs to User relation Test
     *
     * @covers \App\Models\UserManagement\user::interests
     * @group relations/usermanagment
     *
     * @uses \App\Models\UserManagement\User
     *
     * @return void
     */
    public function testInterestUSerBelongsToManyUsers()
    {

    	




    }

}
