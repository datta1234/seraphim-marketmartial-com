<?php

namespace Tests\Unit\Models\UserManagment;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserOtpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * User Model Type Casting test
     *
     * Not accounting for null values within the casting test.
     * @coversNothing
     * @group data
     *
     * @uses   \App\Models\UserManagement\User
     *	 
     * @return void
     */
    public function testUserOtpAttributeCasting()
    {

		$now	= \Carbon\Carbon::now()->toDateTimeString();
    	$user 	= factory( \App\Models\UserManagement\User::class )->create();
    	$otp 	= factory( \App\Models\UserManagement\UserOtp::class )->create([ 'user_id' => $user->id, 'expires_at' => $now ]);
    	
    	$otp_fetch		= \App\Models\UserManagement\UserOtp::find($otp->id);

    	$this->assertInternalType('int', $otp_fetch->id);
    	$this->assertInternalType('int', $otp_fetch->otp);
    	$this->assertInternalType('int', $otp_fetch->user_id);

    	$this->assertNull( 
    		\Validator::make($otp_fetch->toArray(), [
				'expires_at' => 'date_format:Y-m-d H:i:s',
				'created_at' => 'date_format:Y-m-d H:i:s',
				'updated_at' => 'date_format:Y-m-d H:i:s'
			])->validate()
    	);

    }

     /**
     * User Otp belongs To user
     *
     * @covers \App\Models\UserManagement\UserOtp::user
     * @group relations/usermanagment
     *
     * @uses   \App\Models\UserManagement\User
	 *
     * @return void
     */
    public function testOtpBelongsToUser()
    {

    	$now = \Carbon\Carbon::now()->toDateTimeString();
    	$user = factory( \App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('user_otps', [ 'user_id' => $user->id ]);

    	$opt = factory( \App\Models\UserManagement\UserOtp::class )->create([ 'user_id' => $user->id, 'expires_at' => $now ]);
    	$fetch_otp = \App\Models\UserManagement\UserOtp::find( $opt->id );

    	$this->assertEquals( $fetch_otp->user->toArray(), $opt->user->toArray());
    	$this->assertNull( 
    		\Validator::make($fetch_otp->toArray(), [
				'expires_at' => 'date_format:Y-m-d H:i:s',
				'created_at' => 'date_format:Y-m-d H:i:s',
				'updated_at' => 'date_format:Y-m-d H:i:s'
			])->validate()
    	);

    }
}
