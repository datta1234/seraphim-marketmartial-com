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

    		// $this->assertArrayNotHasKey('created_at',$account->toArray() );
    		// $this->assertArrayNotHasKey('updated_at',$account->toArray() );

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

    		// $this->assertArrayNotHasKey('created_at',$market->toArray() );
    		// $this->assertArrayNotHasKey('updated_at',$market->toArray() );

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
    public function testUserHasManySendDistputes()
    {	

    	$send_user 		= factory( \App\Models\UserManagement\User::class  )->create();
    	$receiving_user	= factory( \App\Models\UserManagement\User::class  )->create();
    	$confirmation 	= factory( \App\Models\TradeConfirmations\TradeConfirmation::class )->create();

    	$this->assertDatabaseMissing('distputes', [
			'send_user_id'			=> $send_user->id,
			'receiving_user_id' 	=> $receiving_user->id,
			'trade_confirmation_id'	=> $confirmation->id
		]);

		$distputes = factory( \App\Models\TradeConfirmations\Distpute::class, 5 )->create([
    		'send_user_id'			=> $send_user->id,
    		'receiving_user_id' 	=> $receiving_user->id,
    		'trade_confirmation_id'	=> $confirmation->id
    	])->keyBy('id');

    	$send_user->sendDistputes->keyBy('id')->each( function($item,$key) use ($send_user, $receiving_user, $confirmation, $distputes) {

    		$distpute = $distputes[$key];

    		$this->assertArrayHasKey('title', $distpute->toArray());
    		$this->assertArrayHasKey('send_user_id', $distpute->toArray());
    		$this->assertArrayHasKey('receiving_user_id', $distpute->toArray());
    		$this->assertArrayHasKey('distpute_status_id', $distpute->toArray());
    		$this->assertArrayHasKey('trade_confirmation_id', $distpute->toArray());

    		// $this->assertArrayNotHasKey('created_at',$distputes->toArray() );
    		// $this->assertArrayNotHasKey('updated_at',$distputes->toArray() );

    		$this->assertDatabaseHas('distputes', [

    			'id'								=> $distpute->id,
				'send_user_id'						=> $send_user->id,
				'receiving_user_id'					=> $receiving_user->id,
				'distpute_status_id'				=> $distpute->distpute_status_id,
				'trade_confirmation_id'				=> $confirmation->id

        	]);

    	});

    	$this->assertCount(5, $send_user->sendDistputes);
    	

    }

    /**
     * User has many Receiving Distputes relation Test
     *
     * @covers \App\Models\UserManagement\User::receivingDistputes
     * @testdox User has many \App\Models\TradeConfirmations\Distpute test
     * @group relations/TradeConfirmations
     *
     * @uses   \App\Models\TradeConfirmations\Distpute
	 *
     * @return void
     */
    public function testUserHasManyRecivingDistputes()
    {	

    	$send_user 		= factory( \App\Models\UserManagement\User::class  )->create();
    	$receiving_user	= factory( \App\Models\UserManagement\User::class  )->create();
    	$confirmation 	= factory( \App\Models\TradeConfirmations\TradeConfirmation::class )->create();

    	$this->assertDatabaseMissing('distputes', [
			'send_user_id'			=> $send_user->id,
			'receiving_user_id' 	=> $receiving_user->id,
			'trade_confirmation_id'	=> $confirmation->id
		]);

		$distputes = factory( \App\Models\TradeConfirmations\Distpute::class, 5 )->create([
    		'send_user_id'			=> $send_user->id,
    		'receiving_user_id' 	=> $receiving_user->id,
    		'trade_confirmation_id'	=> $confirmation->id
    	])->keyBy('id');

    	$receiving_user->receivingDistputes->keyBy('id')->each( function($item,$key) use ($send_user, $receiving_user, $confirmation, $distputes) {

    		$distpute = $distputes[$key];

    		$this->assertArrayHasKey('title', $distpute->toArray());
    		$this->assertArrayHasKey('send_user_id', $distpute->toArray());
    		$this->assertArrayHasKey('receiving_user_id', $distpute->toArray());
    		$this->assertArrayHasKey('distpute_status_id', $distpute->toArray());
    		$this->assertArrayHasKey('trade_confirmation_id', $distpute->toArray());

    		// $this->assertArrayNotHasKey('created_at',$distputes->toArray() );
    		// $this->assertArrayNotHasKey('updated_at',$distputes->toArray() );

    		$this->assertDatabaseHas('distputes', [

    			'id'								=> $distpute->id,
				'send_user_id'						=> $send_user->id,
				'receiving_user_id'					=> $receiving_user->id,
				'distpute_status_id'				=> $distpute->distpute_status_id,
				'trade_confirmation_id'				=> $confirmation->id

        	]);

    	});

    	$this->assertCount(5, $receiving_user->receivingDistputes);
    	

    }

     /**
     * User has many User Markets relation Test
     *
     * @covers \App\Models\UserManagement\User::userMarkets
     * @testdox User has many \App\Models\Market\UserMarket test
     * @group relations/Market
     *
     * @uses   \App\Models\Market\UserMarket
	 *
     * @return void
     */
    public function testUserHasManyUserMarkets()
    {	
		
		$user 		= factory( \App\Models\UserManagement\User::class  )->create();

		$this->assertDatabaseMissing('user_markets', [
			'user_id'			=> $user->id
		]);

		$markets 	= factory( \App\Models\Market\UserMarket::class, 5 )->create([
			'user_id'			=> $user->id
		])->keyBy('id');

		$user->userMarkets->keyBy('id')->each( function($item,$key) use ($user, $markets) {

			$market = $markets[$key];

			$this->assertArrayHasKey('user_id', $market->toArray());
			$this->assertArrayHasKey('user_market_request_id', $market->toArray());
			$this->assertArrayHasKey('user_market_status_id', $market->toArray());
			$this->assertArrayHasKey('is_market_maker_notified', $market->toArray());
			$this->assertArrayHasKey('current_market_negotiation_id', $market->toArray());
			$this->assertArrayHasKey('is_trade_away', $market->toArray());


    		$this->assertDatabaseHas('user_markets', [

    			'id'								=> $market->id,
				'user_id'							=> $user->id,
				'user_market_request_id'			=> $market->user_market_request_id,
				'user_market_status_id'				=> $market->user_market_status_id,
				'is_trade_away'						=> $market->is_trade_away,
				'is_market_maker_notified'			=> $market->is_market_maker_notified,
				'current_market_negotiation_id'		=> $market->current_market_negotiation_id,

        	]);


		});

		$this->assertCount(5, $user->userMarkets);


	}


	 /**
     * User has many User Market Subscriptions relation Test
     *
     * @covers \App\Models\UserManagement\User::userMarketSubscriptions
     * @testdox User has many App\Models\Market\UserMarketSubscription test
     * @group relations/Market
     *
     * @uses   \App\Models\Market\UserMarketSubscription
	 *
     * @return void
     */
    public function testUserHasManyUserMarketSubscriptions()
    {	

    	$user 		= factory( \App\Models\UserManagement\User::class  )->create();

    	$this->assertDatabaseMissing('user_market_subscription', [
			'user_id'			=> $user->id
		]);

    	$user_market_subscriptions = factory( \App\Models\Market\UserMarketSubscription::class, 5 )->create([
    		'user_id'	=> $user->id 
    	])->keyBy('id');

    	$user->userMarketSubscriptions->keyBy('id')->each( function($item,$key) use ($user, $user_market_subscriptions) { 

    		$user_market_subscription = $user_market_subscriptions[$key];

    		$this->assertArrayHasKey('user_id', $user_market_subscription->toArray());
			$this->assertArrayHasKey('user_market_id', $user_market_subscription->toArray());

    		$this->assertArrayNotHasKey('deleted_at',$user_market_subscription->toArray() );

    		$this->assertDatabaseHas('user_market_subscription', [

    			'id'				=> $user_market_subscription->id,
				'user_id'			=> $user->id,
				'user_market_id'	=> $user_market_subscription->user_market_id


        	]);

    	});


		$this->assertCount(5, $user->userMarketSubscriptions);


    }


	 /**
     * User has many User Market Negotiations relation Test
     *
     * @covers \App\Models\UserManagement\User::marketNegotiations
     * @testdox User has many App\Models\Market\MarketNegotiation test
     * @group relations/Market
     *
     * @uses   \App\Models\Market\MarketNegotiation
	 *
     * @return void
     */
    public function testUserHasManyMarketNegotiations()
    {	

    	$user = factory( \App\Models\UserManagement\User::class  )->create();

    	$this->assertDatabaseMissing('market_negotiations', [
			'user_id'			=> $user->id
		]);

    	$market_negostiations 	= factory( \App\Models\Market\MarketNegotiation::class, 5 )->create([
    		'user_id'	=> $user->id
    	])->keyBy('id');

    	$user->marketNegotiations->keyBy('id')->each(function($item,$key) use ($user, $market_negostiations) { 

    		$market_negostiation = $market_negostiations[$key];

    		$this->assertArrayHasKey('user_id', $market_negostiation->toArray());
			$this->assertArrayHasKey('market_negotiation_id', $market_negostiation->toArray());
			$this->assertArrayHasKey('user_market_id', $market_negostiation->toArray());
			$this->assertArrayHasKey('bid', $market_negostiation->toArray());
			$this->assertArrayHasKey('offer', $market_negostiation->toArray());
			$this->assertArrayHasKey('bid_qty', $market_negostiation->toArray());
			$this->assertArrayHasKey('offer_qty', $market_negostiation->toArray());
			$this->assertArrayHasKey('bid_premium', $market_negostiation->toArray());
			$this->assertArrayHasKey('offer_premium', $market_negostiation->toArray());
			$this->assertArrayHasKey('has_premium_calc', $market_negostiation->toArray());
			$this->assertArrayHasKey('future_reference', $market_negostiation->toArray());
			$this->assertArrayHasKey('is_repeat', $market_negostiation->toArray());
			$this->assertArrayHasKey('is_accepted', $market_negostiation->toArray());

    		$this->assertDatabaseHas('market_negotiations', [

    			'id'					=> $market_negostiation->id,
				'user_id'				=> $user->id,
				'market_negotiation_id'	=> $market_negostiation->market_negotiation_id,
				'user_market_id'		=> $market_negostiation->user_market_id,
				'bid'					=> $market_negostiation->bid,
				'offer'					=> $market_negostiation->offer,
				'bid_qty'				=> $market_negostiation->bid_qty,
				'offer_qty'				=> $market_negostiation->offer_qty,
				'bid_premium'			=> $market_negostiation->bid_premium,
				'offer_premium'			=> $market_negostiation->offer_premium,
				'has_premium_calc'		=> $market_negostiation->has_premium_calc,
				'future_reference'		=> $market_negostiation->future_reference,
				'is_repeat'				=> $market_negostiation->is_repeat,
				'is_accepted'			=> $market_negostiation->is_accepted,


        	]);


    	});

    	$this->assertCount(5, $user->marketNegotiations);

    }


    /**
     * User has many User Booked Trades relation Test
     *
     * @covers \App\Models\UserManagement\User::bookedTrades
     * @testdox User has many App\Models\TradeConfirmations\BookedTradetest
     * @group relations/TradeConfirmations
     *
     * @uses   \App\Models\TradeConfirmations\BookedTrade
	 *
     * @return void
     */
    public function testUserHasManyBookedTrades()
    {	

    	$user = factory(\App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('booked_trades', [
			'user_id' => $user->id
		]);

    	$booked_trades = factory( \App\Models\TradeConfirmations\BookedTrade::class, 5 )->create([
    		'user_id' => $user->id
    	])->keyBy('id');

    	$user->bookedTrades->keyBy('id')->each( function($item,$key) use ($user, $booked_trades){

    		$booked_trade = $booked_trades[$key];

    		$this->assertArrayHasKey('user_id', $booked_trade->toArray());
    		$this->assertArrayHasKey('trade_confirmation_id', $booked_trade->toArray());
    		$this->assertArrayHasKey('trading_account_id', $booked_trade->toArray());
    		$this->assertArrayHasKey('is_sale', $booked_trade->toArray());
    		$this->assertArrayHasKey('is_rebate', $booked_trade->toArray());
    		$this->assertArrayHasKey('market_id', $booked_trade->toArray());
    		$this->assertArrayHasKey('stock_id', $booked_trade->toArray());
    		$this->assertArrayHasKey('amount', $booked_trade->toArray());
    		$this->assertArrayHasKey('booked_trade_status_id', $booked_trade->toArray());
    		$this->assertArrayHasKey('is_confirmed', $booked_trade->toArray());

    		$this->assertDatabaseHas('booked_trades', [

    			'id'					=> $booked_trade->id,
				'user_id'				=> $user->id,
				'trade_confirmation_id'	=> $booked_trade->trade_confirmation_id,
				'trading_account_id'	=> $booked_trade->trading_account_id,
				'is_sale'				=> $booked_trade->is_sale,
				'is_rebate'				=> $booked_trade->is_rebate,
				'market_id'				=> $booked_trade->market_id,
				'stock_id'				=> $booked_trade->stock_id,
				'amount'				=> $booked_trade->amount,
				'booked_trade_status_id'=> $booked_trade->booked_trade_status_id,
				'is_confirmed'			=> $booked_trade->is_confirmed,

        	]);

    	});

    	$this->assertCount(5, $user->bookedTrades);

    }


	/**
     * User initiates many trade negotiations
     *
     * @covers \App\Models\UserManagement\User::initiateTradeNegotiations
     * @testdox User has many App\Models\Trade\TradeNegotiation
     * @group relations/Trade
     *
     * @uses   \App\Models\Trade\TradeNegotiation
	 *
     * @return void
     */
    public function testUserHasManyInitiateTradeNegotiations()
    {	

    	$initiate_user 	= factory(\App\Models\UserManagement\User::class )->create();
    	$recieving_user = factory(\App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('trade_negotiations', [
			'initiate_user_id' => $initiate_user->id,
			'recieving_user_id' => $recieving_user->id,
		]);

    	$trade_negotiations = factory( \App\Models\Trade\TradeNegotiation::class, 5 )->create([
    		'initiate_user_id' => $initiate_user->id,
			'recieving_user_id' => $recieving_user->id
    	])->keyBy('id');

    	$initiate_user->initiateTradeNegotiations->keyBy('id')->each( function($item,$key) use ($initiate_user,$recieving_user, $trade_negotiations){ 

    		$trade_negosiation = $trade_negotiations[$key];

    		$this->assertArrayHasKey('user_market_id', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('is_offer', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('trade_negotiation_id', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('market_negotiation_id', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('quantity', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('initiate_user_id', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('recieving_user_id', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('is_distpute', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('trade_negotiation_status_id', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('traded', $trade_negosiation->toArray());

    		$this->assertDatabaseHas('trade_negotiations', [

    			'id'						=> $trade_negosiation->id,
				'user_market_id'			=> $trade_negosiation->user_market_id,
				'is_offer'					=> $trade_negosiation->is_offer,
				'trade_negotiation_id'		=> $trade_negosiation->trade_negotiation_id,
				'market_negotiation_id'		=> $trade_negosiation->market_negotiation_id,
				'quantity'					=> $trade_negosiation->quantity,
				'initiate_user_id'			=> $initiate_user->id,
				'recieving_user_id'			=> $recieving_user->id,
				'is_distpute'				=> $trade_negosiation->is_distpute,
				'trade_negotiation_status_id'=> $trade_negosiation->trade_negotiation_status_id,
				'traded'				=> $trade_negosiation->traded,

        	]);

    	});

		$this->assertCount(5, $initiate_user->initiateTradeNegotiations);

    }


    /**
     * User recieving many trade negotiations
     *
     * @covers \App\Models\UserManagement\User::recievingTradeNegotiations
     * @testdox User has many App\Models\Trade\TradeNegotiation
     * @group relations/Trade
     *
     * @uses   \App\Models\Trade\TradeNegotiation
	 *
     * @return void
     */
    public function testUserHasManyRecievingTradeNegotiations()
    {	

		$initiate_user 	= factory(\App\Models\UserManagement\User::class )->create();
    	$recieving_user = factory(\App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('trade_negotiations', [
			'initiate_user_id' => $initiate_user->id,
			'recieving_user_id' => $recieving_user->id,
		]);

    	$trade_negotiations = factory( \App\Models\Trade\TradeNegotiation::class, 5 )->create([
    		'initiate_user_id' => $initiate_user->id,
			'recieving_user_id' => $recieving_user->id
    	])->keyBy('id');

    	$recieving_user->recievingTradeNegotiations->keyBy('id')->each( function($item,$key) use ($initiate_user,$recieving_user, $trade_negotiations){ 

    		$trade_negosiation = $trade_negotiations[$key];

    		$this->assertArrayHasKey('user_market_id', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('is_offer', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('trade_negotiation_id', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('market_negotiation_id', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('quantity', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('initiate_user_id', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('recieving_user_id', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('is_distpute', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('trade_negotiation_status_id', $trade_negosiation->toArray());
    		$this->assertArrayHasKey('traded', $trade_negosiation->toArray());

    		$this->assertDatabaseHas('trade_negotiations', [

    			'id'						=> $trade_negosiation->id,
				'user_market_id'			=> $trade_negosiation->user_market_id,
				'is_offer'					=> $trade_negosiation->is_offer,
				'trade_negotiation_id'		=> $trade_negosiation->trade_negotiation_id,
				'market_negotiation_id'		=> $trade_negosiation->market_negotiation_id,
				'quantity'					=> $trade_negosiation->quantity,
				'initiate_user_id'			=> $initiate_user->id,
				'recieving_user_id'			=> $recieving_user->id,
				'is_distpute'				=> $trade_negosiation->is_distpute,
				'trade_negotiation_status_id'=> $trade_negosiation->trade_negotiation_status_id,
				'traded'					=> $trade_negosiation->traded,

        	]);

    	});

		$this->assertCount(5, $recieving_user->recievingTradeNegotiations);

    }

/*

id: int(11)
trade_negotiation_id:int(11)
market_negotiation_id:int(11)
user_market_id: int(11)
initiate_user_id: int(11)
recieving_user_id: int(11)
trade_status_id: int(11)
quantity: double(11,2)
*/

    /**
     * User initiates many trades
     *
     * @covers \App\Models\UserManagement\User::initiateTrades
     * @testdox User has many App\Models\Trade\Trade
     * @group relations/Trade
     *
     * @uses   App\Models\Trade\Trade
	 *
     * @return void
     */
    public function testUserHasManyInitiateTrades()
    {

    	
    	$initiate_user 	= factory(\App\Models\UserManagement\User::class )->create();
    	$recieving_user = factory(\App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('trades', [
			'initiate_user_id' => $initiate_user->id,
			'recieving_user_id' => $recieving_user->id,
		]);

    	$trades = factory( \App\Models\Trade\Trade::class, 5 )->create([
    		'initiate_user_id' => $initiate_user->id,
			'recieving_user_id' => $recieving_user->id
    	])->keyBy('id');

    	$initiate_user->initiateTrades->keyBy('id')->each( function($item,$key) use ($initiate_user,$recieving_user, $trades){

    		$trade = $trades[$key];

    		$this->assertArrayHasKey('trade_negotiation_id', $trade->toArray());
    		$this->assertArrayHasKey('market_negotiation_id', $trade->toArray());
    		$this->assertArrayHasKey('user_market_id', $trade->toArray());
    		$this->assertArrayHasKey('initiate_user_id', $trade->toArray());
    		$this->assertArrayHasKey('recieving_user_id', $trade->toArray());
    		$this->assertArrayHasKey('trade_status_id', $trade->toArray());
    		$this->assertArrayHasKey('quantity', $trade->toArray());

    		$this->assertDatabaseHas('trades', [

    			'id'						=> $trade->id,
    			'trade_negotiation_id'		=> $trade->trade_negotiation_id,
    			'market_negotiation_id'		=> $trade->market_negotiation_id,
    			'user_market_id'			=> $trade->user_market_id,
    			'initiate_user_id'			=> $initiate_user->id,
    			'recieving_user_id'			=> $recieving_user->id,
    			'trade_status_id'			=> $trade->trade_status_id,
    			'quantity'					=> $trade->quantity,

        	]);

    	});

    	$this->assertCount(5, $initiate_user->initiateTrades);

    }

    /**
     * User recieves many trades
     *
     * @covers \App\Models\UserManagement\User::recievingTrades
     * @testdox User has many App\Models\Trade\Trade
     * @group relations/Trade
     *
     * @uses   App\Models\Trade\Trade
	 *
     * @return void
     */
    public function testUserHasManyRecievingTrades()
    {

    	$initiate_user 	= factory(\App\Models\UserManagement\User::class )->create();
    	$recieving_user = factory(\App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('trades', [
			'initiate_user_id' => $initiate_user->id,
			'recieving_user_id' => $recieving_user->id,
		]);

    	$trades = factory( \App\Models\Trade\Trade::class, 5 )->create([
    		'initiate_user_id' => $initiate_user->id,
			'recieving_user_id' => $recieving_user->id
    	])->keyBy('id');

    	$recieving_user->recievingTrades->keyBy('id')->each( function($item,$key) use ($initiate_user,$recieving_user, $trades){

    		$trade = $trades[$key];

    		$this->assertArrayHasKey('trade_negotiation_id', $trade->toArray());
    		$this->assertArrayHasKey('market_negotiation_id', $trade->toArray());
    		$this->assertArrayHasKey('user_market_id', $trade->toArray());
    		$this->assertArrayHasKey('initiate_user_id', $trade->toArray());
    		$this->assertArrayHasKey('recieving_user_id', $trade->toArray());
    		$this->assertArrayHasKey('trade_status_id', $trade->toArray());
    		$this->assertArrayHasKey('quantity', $trade->toArray());

    		$this->assertDatabaseHas('trades', [

    			'id'						=> $trade->id,
    			'trade_negotiation_id'		=> $trade->trade_negotiation_id,
    			'market_negotiation_id'		=> $trade->market_negotiation_id,
    			'user_market_id'			=> $trade->user_market_id,
    			'initiate_user_id'			=> $initiate_user->id,
    			'recieving_user_id'			=> $recieving_user->id,
    			'trade_status_id'			=> $trade->trade_status_id,
    			'quantity'					=> $trade->quantity,

        	]);

    	});

    	$this->assertCount(5, $recieving_user->recievingTrades);

    }


     /**
     * User has many Rebates
     *
     * @covers \App\Models\UserManagement\User::rebates
     * @testdox User has many App\Models\Trade\Rebate
     * @group relations/Trade
     *
     * @uses   App\Models\Trade\Rebate
	 *
     * @return void
     */
    public function testUserHasManyRebates()
    {

    	$organisation 	= factory( \App\Models\UserManagement\Organisation::class)->create();
    	$user =  factory( \App\Models\UserManagement\User::class )->create([
                        	'organisation_id' =>  $organisation->id
                   		]);


    	$this->assertDatabaseMissing('rebates', [
			'user_id' => $user->id
		]);

		$rebates = factory(\App\Models\Trade\Rebate::class, 5 )->create([
			'user_id' => $user->id,
			'organisation_id' => $organisation->id
		])->keyBy('id');

		$user->rebates->keyBy('id')->each(function($item,$key)use($user, $rebates, $organisation){

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
    			'user_id' => $user->id,
    			'user_market_request_id' => $rebate->user_market_request_id,
    			'user_market_id' => $rebate->user_market_id,
    			'organisation_id' => $organisation->id,
    			'is_paid' => $rebate->is_paid,
    			'booked_trade_id' => $rebate->booked_trade_id,
    			'trade_date' => $rebate->trade_date,

        	]);


		});

		$this->assertCount(5, $user->rebates);

    }

   

    /**
     * User has many Sent trade notification
     *
     * @covers \App\Models\UserManagement\User::sendTradeConfirmations
     * @testdox User has many App\Models\TradeConfirmations\TradeConfirmation
     * @group relations/TradeConfirmations
     *
     * @uses   App\Models\TradeConfirmations\TradeConfirmation
	 *
     * @return void
     */
    public function testUserHasManySendTradeConfirmations()
    {

    	$sending_user 	= factory(\App\Models\UserManagement\User::class )->create();
    	$receiving_user	= factory(\App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('trade_confirmations', [
			'send_user_id' => $sending_user->id,
			'receiving_user_id' => $receiving_user->id
		]);

		$tradeConfirmations = factory( \App\Models\TradeConfirmations\TradeConfirmation::class , 5)->create([
			'send_user_id' => $sending_user->id,
			'receiving_user_id' => $receiving_user->id
		])->keyby('id');

		$sending_user->sendTradeConfirmations->keyBy('id')->each( function($item,$key)use($sending_user,$receiving_user,$tradeConfirmations){

			$trade_confirmation = $tradeConfirmations[$key];

			$this->assertArrayHasKey('send_user_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('receiving_user_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('trade_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('spot_price', $trade_confirmation->toArray());
			$this->assertArrayHasKey('future_reference', $trade_confirmation->toArray());
			$this->assertArrayHasKey('near_expiery_reference', $trade_confirmation->toArray());
			$this->assertArrayHasKey('trade_confirmation_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('trade_confirmation_status_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('market_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('stock_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('contracts', $trade_confirmation->toArray());
			$this->assertArrayHasKey('puts', $trade_confirmation->toArray());
			$this->assertArrayHasKey('calls', $trade_confirmation->toArray());
			$this->assertArrayHasKey('puts', $trade_confirmation->toArray());
			$this->assertArrayHasKey('delta', $trade_confirmation->toArray());
			$this->assertArrayHasKey('gross_premiums', $trade_confirmation->toArray());
			$this->assertArrayHasKey('net_premiums', $trade_confirmation->toArray());
			$this->assertArrayHasKey('is_confirmed', $trade_confirmation->toArray());
			$this->assertArrayHasKey('traiding_account_id', $trade_confirmation->toArray());

			$this->assertDatabaseHas('trade_confirmations', [

    			'id' => $trade_confirmation->id,
    			'send_user_id' => $sending_user->id,
    			'receiving_user_id' => $receiving_user->id,
    			'trade_id' => $trade_confirmation->trade_id,
    			'spot_price' => $trade_confirmation->spot_price,
    			'future_reference' => $trade_confirmation->future_reference,
    			'near_expiery_reference' => $trade_confirmation->near_expiery_reference,
    			'trade_confirmation_id' => $trade_confirmation->trade_confirmation_id,
    			'trade_confirmation_status_id' => $trade_confirmation->trade_confirmation_status_id,
    			'market_id' => $trade_confirmation->market_id,
    			'stock_id' => $trade_confirmation->stock_id,
    			'contracts' => $trade_confirmation->contracts,
    			'puts' => $trade_confirmation->puts,
    			'calls' => $trade_confirmation->calls,
    			'delta' => $trade_confirmation->delta,
    			'gross_premiums' => $trade_confirmation->gross_premiums,
    			'net_premiums' => $trade_confirmation->net_premiums,
    			'is_confirmed' => $trade_confirmation->is_confirmed,
    			'traiding_account_id' => $trade_confirmation->traiding_account_id,

        	]);

		});

		$this->assertCount(5, $sending_user->sendTradeConfirmations );

    }

    /**
     * User has many recieved trade notifications
     *
     * @covers \App\Models\UserManagement\User::recievingTradeConfirmations
     * @testdox User has many App\Models\TradeConfirmations\TradeConfirmation
     * @group relations/TradeConfirmations
     *
     * @uses   App\Models\TradeConfirmations\TradeConfirmation
	 *
     * @return void
     */
    public function testUserHasManyRecievingTradeConfirmations()
    {

    	$sending_user 	= factory(\App\Models\UserManagement\User::class )->create();
    	$receiving_user	= factory(\App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('trade_confirmations', [
			'send_user_id' => $sending_user->id,
			'receiving_user_id' => $receiving_user->id
		]);

		$tradeConfirmations = factory( \App\Models\TradeConfirmations\TradeConfirmation::class , 5)->create([
			'send_user_id' => $sending_user->id,
			'receiving_user_id' => $receiving_user->id
		])->keyby('id');

		$receiving_user->recievingTradeConfirmations->keyBy('id')->each( function($item,$key)use($sending_user,$receiving_user,$tradeConfirmations){

			$trade_confirmation = $tradeConfirmations[$key];

			$this->assertArrayHasKey('send_user_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('receiving_user_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('trade_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('spot_price', $trade_confirmation->toArray());
			$this->assertArrayHasKey('future_reference', $trade_confirmation->toArray());
			$this->assertArrayHasKey('near_expiery_reference', $trade_confirmation->toArray());
			$this->assertArrayHasKey('trade_confirmation_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('trade_confirmation_status_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('market_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('stock_id', $trade_confirmation->toArray());
			$this->assertArrayHasKey('contracts', $trade_confirmation->toArray());
			$this->assertArrayHasKey('puts', $trade_confirmation->toArray());
			$this->assertArrayHasKey('calls', $trade_confirmation->toArray());
			$this->assertArrayHasKey('puts', $trade_confirmation->toArray());
			$this->assertArrayHasKey('delta', $trade_confirmation->toArray());
			$this->assertArrayHasKey('gross_premiums', $trade_confirmation->toArray());
			$this->assertArrayHasKey('net_premiums', $trade_confirmation->toArray());
			$this->assertArrayHasKey('is_confirmed', $trade_confirmation->toArray());
			$this->assertArrayHasKey('traiding_account_id', $trade_confirmation->toArray());

			$this->assertDatabaseHas('trade_confirmations', [

    			'id' => $trade_confirmation->id,
    			'send_user_id' => $sending_user->id,
    			'receiving_user_id' => $receiving_user->id,
    			'trade_id' => $trade_confirmation->trade_id,
    			'spot_price' => $trade_confirmation->spot_price,
    			'future_reference' => $trade_confirmation->future_reference,
    			'near_expiery_reference' => $trade_confirmation->near_expiery_reference,
    			'trade_confirmation_id' => $trade_confirmation->trade_confirmation_id,
    			'trade_confirmation_status_id' => $trade_confirmation->trade_confirmation_status_id,
    			'market_id' => $trade_confirmation->market_id,
    			'stock_id' => $trade_confirmation->stock_id,
    			'contracts' => $trade_confirmation->contracts,
    			'puts' => $trade_confirmation->puts,
    			'calls' => $trade_confirmation->calls,
    			'delta' => $trade_confirmation->delta,
    			'gross_premiums' => $trade_confirmation->gross_premiums,
    			'net_premiums' => $trade_confirmation->net_premiums,
    			'is_confirmed' => $trade_confirmation->is_confirmed,
    			'traiding_account_id' => $trade_confirmation->traiding_account_id,

        	]);


		});

		$this->assertCount(5, $receiving_user->recievingTradeConfirmations );


    }

}
