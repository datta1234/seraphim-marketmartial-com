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
 	 *
     * @uses   \App\Models\UserManagement\Organisation
     *	 
     * @return void
     */
    public function testAttributeCasting()
    {

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
	 *
     * @return void
     */
    public function testHiddenAttributes()
    {

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
 *
     *
     * @return void
     */
    public function testUserBelongsToRole()
    {

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
     * @uses   \App\Models\UserManagement\Organisation
	 *	 *
     * @return void
     */
    public function testUserBelongsToOrganisation()
    {	

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
     * @uses   \App\Models\UserManagement\Interest
	 *
     * @return void
     */
    public function testUserBelongsToManyInterests()
    {	

    	
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
    		$this->assertArrayHasKey('title', $interest->toArray());
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
     * @uses   \App\Models\UserManagement\Email
	 *
     * @return void
     */
    public function testUserHasManyEmails()
    {	

    	$user 			= factory( \App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('emails', [
            	'user_id' => $user->id
        	]);

    	$emails = factory( \App\Models\UserManagement\Email::class, 5 )->create([ 'user_id' => $user->id ]);

    	foreach ($emails as $email ) {

    		$this->assertArrayHasKey('email', $email->toArray());
			$this->assertArrayHasKey('user_id', $email->toArray());
			$this->assertArrayHasKey('title', $email->toArray());
			$this->assertArrayHasKey('default_id', $email->toArray());
			$this->assertArrayHasKey('notifiable', $email->toArray());

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
     * @uses   \App\Models\UserManagement\UserOtps
	 *
     * @return void
     */
    public function testUserHasManyUserOtp()
    {	

    	$now 			= Carbon::now()->toDateTimeString();
    	$user 			= factory( \App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('user_otps', [ 'user_id' => $user->id ]);

    	$user_otps		= factory( \App\Models\UserManagement\UserOtp::class, 5 )->create([ 'user_id' => $user->id, 'expires_at' => $now ]);

    	$user->load('userOtps')->each(function ($item, $key) use ($user_otps, $user) {

    		$item->userOtps->each( function($item, $key)  use ($user_otps, $user) {

    			$this->assertArrayHasKey('otp', $item->toArray());
    			$this->assertArrayHasKey('user_id', $item->toArray());
    			$this->assertArrayHasKey('expires_at', $item->toArray());

	    		$this->assertDatabaseHas('user_otps', [

	    			'id'		=> $user_otps[$key]->id,
	    			'otp'		=> $user_otps[$key]->otp,
	    			'user_id'	=> $user->id,
	    			'expires_at'=> $user_otps[$key]->expires_at,
	            	
	        	]);

    		});

		});

		$this->assertCount(0, ['foo']);

    }

     /**
     * User emails has many UserNotification relation Test
     *
     * @covers \App\Models\UserManagement\User::userNotifications
     * @testdox User has many \App\Models\UserManagement\userNotifications test
     * @group relations/usermanagment
     *
     * @uses   \App\Models\UserManagement\userNotifications
	 *
     * @return void
     */
    public function testUserHasManyUserNotification()
    {	

    	$user 			= factory( \App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('user_notifications', [
			'user_id' => $user->id
        ]);

        $user_notifiactions = factory( \App\Models\UserManagement\UserNotification::class, 5 )->create([ 'user_id' => $user->id ]);

        $user->load('userNotifications')->each(function ($item, $key) use ($user_notifiactions, $user) {

        	$item->userNotifications->each( function($item, $key)  use ($user_notifiactions, $user) {

    			$this->assertArrayHasKey('user_id', $item->toArray());
    			$this->assertArrayHasKey('data', $item->toArray());
    			$this->assertArrayHasKey('description', $item->toArray());
    			$this->assertArrayHasKey('type', $item->toArray());
    			$this->assertArrayHasKey('notifiable_id', $item->toArray());
    			$this->assertArrayHasKey('user_notification_type_id', $item->toArray());
    			$this->assertArrayHasKey('notifiable_type', $item->toArray());
    			$this->assertArrayHasKey('read_at', $item->toArray());

	    		$this->assertDatabaseHas('user_notifications', [

	    			'id'						=> $user_notifiactions[$key]->id,
	    			'user_id'					=> $user_id,
	    			'data'						=> $user_notifiactions[$key]->data,
	    			'description'				=> $user_notifiactions[$key]->description,
	    			'type'						=> $user_notifiactions[$key]->type,
	    			'notifiable_id'				=> $user_notifiactions[$key]->notifiable_id,
	    			'user_notification_type_id'	=> $user_notifiactions[$key]->user_notification_type_id,
	    			'notifiable_type'			=> $user_notifiactions[$key]->notifiable_type,
	    			'read_at'					=> $user_notifiactions[$key]->read_at,
            	
        		]);

    		});

		});

    }

	/**
     * User market watch belongs to many relation Test
     *
     * @covers \App\Models\UserManagement\User::marketWatched
     * @testdox User belongs to many \App\Models\StructureItems\Market test
     * @group relations/structureItems
     *
     * @uses   \App\Models\StructureItems\Market
	 *
     * @return void
     */
    public function testUserBelongsToManyUserWatchedMarkets()
    {	

    	$user 			= factory( \App\Models\UserManagement\User::class )->create();
    	$markets 		= factory( \App\Models\StructureItems\Market::class, 5 )->create();

    	$markets->each( function($item, $key) use ($user) {

    		$this->assertDatabaseMissing('user_market_watched', [
				'user_id' 	=> $user->id,
				'market_id'	=> $item->id
        	]);

    	});

		$user->marketWatched()->attach( $markets );
    	$user->marketWatched()->sync( $markets );
    	$user->load('marketWatched');

    	$user->marketWatched->each( function($item,$key) use ($user, $markets) {

			$this->assertDatabaseHas('user_market_watched', [
				'user_id'	=> $user->id,
				'market_id' => $markets[$key]->id
			]);

			$this->assertArrayHasKey('title', $item->toArray());
    		$this->assertArrayHasKey('is_seldom', $item->toArray());
    		$this->assertArrayHasKey('market_type_id', $item->toArray());
    		$this->assertArrayHasKey('description', $item->toArray());
    		$this->assertArrayHasKey('has_deadline', $item->toArray());
    		$this->assertArrayHasKey('has_negotiation', $item->toArray());
    		$this->assertArrayHasKey('has_rebate', $item->toArray());

			$this->assertDatabaseHas('markets', [
				'id'				=> $markets[$key]->id,
				'title' 			=> $markets[$key]->title,
				'market_type_id' 	=> $markets[$key]->market_type_id,
				'description' 		=> $markets[$key]->description,
				'has_deadline' 		=> $markets[$key]->has_deadline,
				'has_negotiation' 	=> $markets[$key]->has_negotiation,
				'has_rebate' 		=> $markets[$key]->has_rebate,
			]);

    	});

    	$this->assertCount(5, $user->marketWatched->toArray());


	}

	/**
     * User market interests belongs to many relation Test
     *
     * @covers \App\Models\UserManagement\User::marketInterests
     * @testdox User belongs to many \App\Models\StructureItems\Market test
     * @group relations/structureItems
     *
     * @uses   \App\Models\StructureItems\Market
	 *
     * @return void
     */
    public function testUserBelongsToManyUserInterestsMarket()
    {	

		$faker 			= Faker::create();
    	$filler 		= $faker->word;
    	$user 			= factory( \App\Models\UserManagement\User::class )->create();
    	$markets 		= factory( \App\Models\StructureItems\Market::class, 5 )->create();

    	$markets->each( function($item, $key) use ($user) {

    		$this->assertDatabaseMissing('user_market_interests', [
				'user_id' 	=> $user->id,
				'market_id'	=> $item->id
        	]);

    	});

		$user->marketInterests()->attach( $markets );
    	$user->marketInterests()->sync( $markets );
    	$user->load('marketInterests');

    	$user->marketInterests->each( function($item,$key) use ($user, $markets) {

			$this->assertDatabaseHas('user_market_interests', [
				'user_id'	=> $user->id,
				'market_id' => $markets[$key]->id
			]);

			$this->assertArrayHasKey('title', $item->toArray());
    		$this->assertArrayHasKey('is_seldom', $item->toArray());
    		$this->assertArrayHasKey('market_type_id', $item->toArray());
    		$this->assertArrayHasKey('description', $item->toArray());
    		$this->assertArrayHasKey('has_deadline', $item->toArray());
    		$this->assertArrayHasKey('has_negotiation', $item->toArray());
    		$this->assertArrayHasKey('has_rebate', $item->toArray());

			$this->assertDatabaseHas('markets', [
				'id'				=> $markets[$key]->id,
				'title' 			=> $markets[$key]->title,
				'market_type_id' 	=> $markets[$key]->market_type_id,
				'description' 		=> $markets[$key]->description,
				'has_deadline' 		=> $markets[$key]->has_deadline,
				'has_negotiation' 	=> $markets[$key]->has_negotiation,
				'has_rebate' 		=> $markets[$key]->has_rebate,
			]);

    	});

    	$this->assertCount(5, $user->marketInterests->toArray());


	}

	/**
     * User has many TradingAccount relation Test
     *
     * @covers \App\Models\UserManagement\User::tradingAccounts
     * @testdox User has many \App\Models\UserManagement\TradingAccount test
     * @group relations/UserManagement
     *
     * @uses   \App\Models\UserManagement\TradingAccount
	 *
     * @return void
     */
    public function testUserHasManyTradeAccounts()
    {	

		$faker 			= Faker::create();
    	$filler 		= $faker->swiftBicNumber;
    	$user 			= factory( \App\Models\UserManagement\User::class )->create();

		$this->assertDatabaseMissing('trading_accounts', [
			'user_id'	=> $user->id,
		]);

    	$trade_accounts = factory( \App\Models\UserManagement\TradingAccount::class, 5 )->create([
    		'user_id'		=> $user->id,
    		'safex_number'	=> $faker->swiftBicNumber,
    		'sub_account'	=> $faker->swiftBicNumber,
    	])->keyBy('id');

    	$user->tradingAccounts->keyBy('id')->each( function($item,$key) use ($user, $trade_accounts) {

    		$account = $trade_accounts[$key];

    		$this->assertArrayHasKey('user_id', $account->toArray());
    		$this->assertArrayHasKey('market_id', $account->toArray());
    		$this->assertArrayHasKey('safex_number', $account->toArray());
    		$this->assertArrayHasKey('sub_account', $account->toArray());

    		$this->assertArrayNotHasKey('created_at',$account->toArray() );
    		$this->assertArrayNotHasKey('updated_at',$account->toArray() );

    		$this->assertDatabaseHas('trading_accounts', [
    			'id'			=> $account->id,
				'user_id' 		=> $user->id,
				'market_id'		=> $account->market_id,
				'safex_number'	=> $account->safex_number,
				'sub_account'	=> $account->sub_account,
        	]);

    	});

    	$this->assertCount(5, $trade_accounts);



	}

	/**
     * User has many Market Requests relation Test
     *
     * @covers \App\Models\UserManagement\User::userMarketRequests
     * @testdox User has many \App\Models\MarketRequest\UserMarketRequest test
     * @group relations/MarketRequest
     *
     * @uses   \App\Models\MarketRequest\UserMarketRequest
	 *
     * @return void
     */
    public function testUserHasManyUserMarketRequests()
    {	

    	$faker 			= Faker::create();
    	$filler 		= $faker->swiftBicNumber;
    	$user 			= factory( \App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('user_market_requests', [
			'user_id'	=> $user->id,
		]);

		$markets = factory( \App\Models\MarketRequest\UserMarketRequest::class, 5 )->create([
    		'user_id'		=> $user->id
    	])->keyBy('id');

    	$user->userMarketRequests->keyBy('id')->each( function($item,$key) use ($user, $markets) {

    		$market = $markets[$key];

    		$this->assertArrayHasKey('user_id', $market->toArray());
    		$this->assertArrayHasKey('trade_structure_id', $market->toArray());
    		$this->assertArrayHasKey('user_market_request_statuses_id', $market->toArray());
    		$this->assertArrayHasKey('chosen_user_market_id', $market->toArray());

    		$this->assertArrayNotHasKey('created_at',$market->toArray() );
    		$this->assertArrayNotHasKey('updated_at',$market->toArray() );

    		$this->assertDatabaseHas('user_market_requests', [
    			'id'								=> $market->id,
				'user_id' 							=> $user->id,
				'trade_structure_id'				=> $market->trade_structure_id,
				'user_market_request_statuses_id'	=> $market->user_market_request_statuses_id,
				'chosen_user_market_id'				=> $market->chosen_user_market_id,
        	]);

    	});

    	$this->assertCount(5, $user->userMarketRequests);

    }


	/**
     * User has many Send Distputes relation Test
     *
     * @covers \App\Models\UserManagement\User::sendDistputes
     * @testdox User has many \App\Models\TradeConfirmations\Distpute test
     * @group relations/TradeConfirmations
     *
     * @uses   \App\Models\TradeConfirmations\Distpute
	 *
     * @return void
     */
    public function testUserHasManyUserMarketRequests()
    {	

    	$faker 			= Faker::create();
    	$filler 		= $faker->swiftBicNumber;
    	$user 			= factory( \App\Models\TradeConfirmations\Distpute::class )->create();

    	$this->assertDatabaseMissing('distputes', [
			'send_user_id'	=> $user->id,
		]);

		$markets = factory( \App\Models\MarketRequest\UserMarketRequest::class, 5 )->create([
    		'user_id'		=> $user->id
    	])->keyBy('id');

    	$user->userMarketRequests->keyBy('id')->each( function($item,$key) use ($user, $markets) {

    		$market = $markets[$key];

    		$this->assertArrayHasKey('user_id', $market->toArray());
    		$this->assertArrayHasKey('trade_structure_id', $market->toArray());
    		$this->assertArrayHasKey('user_market_request_statuses_id', $market->toArray());
    		$this->assertArrayHasKey('chosen_user_market_id', $market->toArray());

    		$this->assertArrayNotHasKey('created_at',$market->toArray() );
    		$this->assertArrayNotHasKey('updated_at',$market->toArray() );

    		$this->assertDatabaseHas('', [
    			'id'								=> $market->id,
				'user_id' 							=> $user->id,
				'trade_structure_id'				=> $market->trade_structure_id,
				'user_market_request_statuses_id'	=> $market->user_market_request_statuses_id,
				'chosen_user_market_id'				=> $market->chosen_user_market_id,
        	]);

    	});

    	$this->assertCount(5, $user->userMarketRequests);

    }


}
