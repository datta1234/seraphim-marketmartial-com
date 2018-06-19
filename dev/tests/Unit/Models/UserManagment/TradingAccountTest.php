<?php

namespace Tests\Unit\Models\UserManagment;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TradingAccountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * User Model Type Casting test
     *
     * Not accounting for null values within the casting test.
     * @coversNothing
     * @testdox check App\Models\UserManagement\TradingAccount::class casting
     * @group data
     *
     *	 
     * @return void
     */
    public function testTradingAccountCasting()
    {

    	$tradeing_account 	= factory( \App\Models\UserManagement\TradingAccount::class )->create();
    	$fetch_tradeing_account = \App\Models\UserManagement\TradingAccount::find( $tradeing_account->id );

    	$this->assertInternalType('int', $fetch_tradeing_account->id);
    	$this->assertInternalType('int', $fetch_tradeing_account->user_id);
    	$this->assertInternalType('int', $fetch_tradeing_account->market_id);
    	$this->assertInternalType('string', $fetch_tradeing_account->safex_number);
    	$this->assertInternalType('string', $fetch_tradeing_account->sub_account);
    	$this->assertNull( 
    		\Validator::make($fetch_tradeing_account->toArray(), [
				'created_at' => 'date_format:Y-m-d H:i:s',
				'updated_at' => 'date_format:Y-m-d H:i:s'
			])->validate()
    	);

    }

	/**
     * Trade account belongs To user
     *
     * @covers \App\Models\UserManagement\TradingAccount::user
     * @testdox User belongs to \App\Models\UserManagement\User test
     * @group relations/usermanagment
     *
     * @uses   \App\Models\UserManagement\User
	 *
     * @return void
     */
    public function testTradeAccountBelongsToUser()
    {

    	$user = factory( \App\Models\UserManagement\User::class )->create();

    	$this->assertDatabaseMissing('trading_accounts', [ 'user_id' => $user->id ]);

		$tradeing_account = factory( \App\Models\UserManagement\TradingAccount::class )->create([ 'user_id' => $user->id ]);
    	$fetch_tradeing_account = \App\Models\UserManagement\TradingAccount::find( $tradeing_account->id );

		$this->assertEquals( $fetch_tradeing_account->user->toArray(), $tradeing_account->user->toArray());

    	$this->assertArrayHasKey('user_id', $fetch_tradeing_account->toArray());
    	$this->assertArrayHasKey('market_id', $fetch_tradeing_account->toArray());
    	$this->assertArrayHasKey('safex_number', $fetch_tradeing_account->toArray());
    	$this->assertArrayHasKey('sub_account', $fetch_tradeing_account->toArray());

		$this->assertDatabaseHas('trading_accounts', [
			'user_id'          	=> $fetch_tradeing_account->user->id,
			'market_id' 		=> $fetch_tradeing_account->market_id,
			'safex_number'		=> $fetch_tradeing_account->safex_number,
			'sub_account'		=> $fetch_tradeing_account->sub_account
		]);

    	$this->assertNull( 
    		\Validator::make($fetch_tradeing_account->toArray(), [
				'created_at' => 'date_format:Y-m-d H:i:s',
				'updated_at' => 'date_format:Y-m-d H:i:s'
			])->validate()
    	);

    }


	/**
     * market belongs To user
     *
     * @covers \App\Models\UserManagement\TradingAccount::market
     * @testdox User belongs to \App\Models\UserManagement\User test
     * @group relations/Market
     *
     * @uses   App\Models\StructureItems\Market
	 *
     * @return void
     */
    public function testMarketBelongsToUser()
    {

    	$market = factory( \App\Models\StructureItems\Market::class )->create();

    	$this->assertDatabaseMissing('trading_accounts', [ 'market_id' => $market->id ]);

		$tradeing_account = factory( \App\Models\UserManagement\TradingAccount::class )->create([ 'market_id' => $market->id ]);
    	$fetch_tradeing_account = \App\Models\UserManagement\TradingAccount::find( $tradeing_account->id );

		$this->assertEquals( $fetch_tradeing_account->market->toArray(), $tradeing_account->market->toArray());

    	$this->assertArrayHasKey('user_id', $fetch_tradeing_account->toArray());
    	$this->assertArrayHasKey('market_id', $fetch_tradeing_account->toArray());
    	$this->assertArrayHasKey('safex_number', $fetch_tradeing_account->toArray());
    	$this->assertArrayHasKey('sub_account', $fetch_tradeing_account->toArray());

		$this->assertDatabaseHas('trading_accounts', [
			'user_id'          	=> $fetch_tradeing_account->user->id,
			'market_id' 		=> $market->id,
			'safex_number'		=> $fetch_tradeing_account->safex_number,
			'sub_account'		=> $fetch_tradeing_account->sub_account
		]);

    	$this->assertNull( 
    		\Validator::make($fetch_tradeing_account->toArray(), [
				'created_at' => 'date_format:Y-m-d H:i:s',
				'updated_at' => 'date_format:Y-m-d H:i:s'
			])->validate()
    	);

    }

    /**
     * Trade account has many booked trades
     *
     * @covers \App\Models\UserManagement\TradingAccount::bookedTrades
     * @testdox trade account has many App\Models\TradeConfirmations\BookedTrade test
     * @group relations/TradeConfirmations
     *
     * @uses   App\Models\TradeConfirmations\BookedTrade
	 *
     * @return void
     */
    public function testTradeAccountHasManyBookedTrades()
    {

    	$tradeing_account = factory( \App\Models\UserManagement\TradingAccount::class )->create();
    	$trade_confirmation = factory( \App\Models\TradeConfirmations\TradeConfirmation::class )->create();
    	$this->assertDatabaseMissing('booked_trades', [ 'trading_account_id' => $tradeing_account->id ]);

    	$booked_trades = factory( \App\Models\TradeConfirmations\BookedTrade::class, 5 )->create([
    		'trading_account_id' => $tradeing_account->id,
    		'trade_confirmation_id' => $trade_confirmation->id
    	])->keyBy('id');

    	$fetch_tradeing_account = \App\Models\UserManagement\TradingAccount::find( $tradeing_account->id );
    	$fetch_tradeing_account->bookedTrades->keyBy('id')->each( function($item,$key) use ($booked_trades,$fetch_tradeing_account,$tradeing_account) {

    		$boooked_trade = $booked_trades[$key];

	    	$this->assertEquals( $item->toArray(), $boooked_trade->toArray());	

	    	$this->assertArrayHasKey('user_id', $item->toArray());
    		$this->assertArrayHasKey('trade_confirmation_id', $item->toArray());
    		$this->assertArrayHasKey('trading_account_id', $item->toArray());
    		$this->assertArrayHasKey('is_sale', $item->toArray());
    		$this->assertArrayHasKey('is_rebate', $item->toArray());
    		$this->assertArrayHasKey('market_id', $item->toArray());
    		$this->assertArrayHasKey('stock_id', $item->toArray());
    		$this->assertArrayHasKey('amount', $item->toArray());
    		$this->assertArrayHasKey('booked_trade_status_id', $item->toArray());
    		$this->assertArrayHasKey('is_confirmed', $item->toArray());

    		$this->assertDatabaseHas('booked_trades', [
    			'id' =>  $boooked_trade->id,
				'user_id' => $boooked_trade->user_id,
				'trade_confirmation_id' => $boooked_trade->trade_confirmation_id,
				'trading_account_id' => $tradeing_account->id,
				'is_sale' => $boooked_trade->is_sale,
				'is_rebate' => $boooked_trade->is_rebate,
				'market_id' => $boooked_trade->market_id,
				'stock_id' => $boooked_trade->stock_id,
				'amount' => $boooked_trade->amount,
				'booked_trade_status_id' => $boooked_trade->booked_trade_status_id,
				'is_confirmed' => $boooked_trade->is_confirmed,
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
     * Trade account has many trade confirmation
     *
     * @covers \App\Models\UserManagement\TradingAccount::tradeConfirmations
     * @testdox trade account has many App\Models\TradeConfirmations\TradeConfirmation test
     * @group relations/TradeConfirmations
     *
     * @uses   App\Models\TradeConfirmations\TradeConfirmation
	 *
     * @return void
     */
    public function testTradeAccountHasManyTradeConfirmations()
    {

    	$tradeing_account = factory( \App\Models\UserManagement\TradingAccount::class )->create();
    	$trade =factory( \App\Models\Trade\Trade::class)->create();

    	$this->assertDatabaseMissing('trade_confirmations', [ 'trading_account_id' => $tradeing_account->id ]);

    	$trade_Confirmations = factory( \App\Models\TradeConfirmations\TradeConfirmation::class, 5 )->create([
    		'trading_account_id' => $tradeing_account->id,
    		'trade_id' => $trade->id
    	])->keyBy('id');

    	$tradeing_account->tradeConfirmations->keyBy('id')->each( function($item,$key) use ($tradeing_account,$trade_Confirmations, $trade) {

    		$trade_confirmation = $trade_Confirmations[$key];

	    	$this->assertEquals( $item->toArray(), $trade_confirmation->toArray());	

			$this->assertArrayHasKey('send_user_id', $item->toArray());
    		$this->assertArrayHasKey('receiving_user_id', $item->toArray());
    		$this->assertArrayHasKey('trade_id', $item->toArray());
    		$this->assertArrayHasKey('spot_price', $item->toArray());
    		$this->assertArrayHasKey('future_reference', $item->toArray());
    		$this->assertArrayHasKey('near_expiery_reference', $item->toArray());
    		$this->assertArrayHasKey('trade_confirmation_id', $item->toArray());
    		$this->assertArrayHasKey('market_id', $item->toArray());
    		$this->assertArrayHasKey('stock_id', $item->toArray());
    		$this->assertArrayHasKey('contracts', $item->toArray());
    		$this->assertArrayHasKey('puts', $item->toArray());
    		$this->assertArrayHasKey('calls', $item->toArray());
    		$this->assertArrayHasKey('delta', $item->toArray());
    		$this->assertArrayHasKey('gross_premiums', $item->toArray());
    		$this->assertArrayHasKey('net_premiums', $item->toArray());
    		$this->assertArrayHasKey('is_confirmed', $item->toArray());
    		$this->assertArrayHasKey('trading_account_id', $item->toArray());

    		$this->assertDatabaseHas('trade_confirmations', [
    			'id' =>  $trade_confirmation->id,
				'send_user_id' => $trade_confirmation->send_user_id,
				'receiving_user_id' => $trade_confirmation->receiving_user_id,
				'trade_id' => $trade->id,
				'spot_price' => $trade_confirmation->spot_price,
				'future_reference' => $trade_confirmation->future_reference,
				'near_expiery_reference' => $trade_confirmation->near_expiery_reference,
				'trade_confirmation_id' => $trade_confirmation->trade_confirmation_id,
				'market_id' => $trade_confirmation->market_id,
				'stock_id' => $trade_confirmation->stock_id,
				'contracts' => $trade_confirmation->contracts,
				'puts' => $trade_confirmation->puts,
				'calls' => $trade_confirmation->calls,
				'delta' => $trade_confirmation->delta,
				'gross_premiums' => $trade_confirmation->gross_premiums,
				'net_premiums' => $trade_confirmation->net_premiums,
				'is_confirmed' => $trade_confirmation->is_confirmed,
				'trading_account_id' => $tradeing_account->id,
			]);
			
	    	$this->assertNull( 
				\Validator::make($item->toArray(), [
					'created_at' => 'date_format:Y-m-d H:i:s',
					'updated_at' => 'date_format:Y-m-d H:i:s'
				])->validate()
			);

		});


    }

}
