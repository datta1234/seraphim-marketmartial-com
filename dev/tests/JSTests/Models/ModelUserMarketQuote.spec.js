import UserMarketQuote from '../../../resources/assets/js/lib/UserMarketQuote.js';
import UserMarketRequest from '../../../resources/assets/js/lib/UserMarketRequest.js';

describe('class UserMarketQuote', () => {

	//TODO - move test data out to json file
	let user_market_quote = null;
	let market_quote_data = {
		id: 2,
		is_maker: true,
    	is_interest: true,
        bid_only: true,
        offer_only: true,
        vol_spread: "4",
        time: "13:20:45",
        bid_qty: 500,
        bid: 859,
        offer: 8587,
        offer_qty: 590
	};

	beforeEach(function() {
    	user_market_quote = new UserMarketQuote(market_quote_data);	
  	});

	describe('UserMarketQuote constructor', () => {

		it('UserMarketRequest constructed with defaults', () => {
			let default_user_user_market_quote = new UserMarketQuote();
			chai.assert.equal(default_user_user_market_quote.id, '', 'id property is default value');
			chai.assert.isFalse(default_user_user_market_quote.is_maker, 'is_maker property is default value');
			chai.assert.isFalse(default_user_user_market_quote.is_interest, 'is_interest property is default value');
			chai.assert.isFalse(default_user_user_market_quote.bid_only, 'bid_only property is default value');
			chai.assert.isFalse(default_user_user_market_quote.offer_only, 'offer_only property is default value');
			chai.assert.isNull(default_user_user_market_quote.vol_spread, 'vol_spread_state property is default value');
			//chai.assert(moment.isMoment(default_user_user_market_quote.time),'created_at should be of type moment');
			chai.assert.equal(default_user_user_market_quote.time, '', 'time property is default value');
			chai.assert.isNull(default_user_user_market_quote.bid_qty, 'bid_qty property is default value');
			chai.assert.isNull(default_user_user_market_quote.bid, 'bid property is default value');
			chai.assert.isNull(default_user_user_market_quote.offer, 'offer property is default value');
			chai.assert.isNull(default_user_user_market_quote.offer_qty, 'offer_qty property is default value');
		});

		it('UserMarketRequest constructed with passed params', () => {
			chai.assert.equal(user_market_quote.id, market_quote_data.id, 'id property is equal to passed id value');
			chai.assert.equal(user_market_quote.is_maker, market_quote_data.is_maker, 'is_maker property is equal to passed is_maker value');
			chai.assert.equal(user_market_quote.is_interest, market_quote_data.is_interest, 'is_interest property is equal to passed is_interest value');
			chai.assert.equal(user_market_quote.bid_only, market_quote_data.bid_only, 'bid_only property is equal to passed bid_only value');
			chai.assert.equal(user_market_quote.offer_only, market_quote_data.offer_only, 'offer_only property is equal to passed offer_only value');
			chai.assert.equal(user_market_quote.vol_spread, market_quote_data.vol_spread, 'vol_spread property is equal to passed vol_spread value');
			//chai.assert(user_market_quote.created_at.isSame(market_quote_data.created_at),'created_at property is equal to passed created_at value');
			chai.assert.equal(user_market_quote.time, market_quote_data.time, 'time property is equal to passed time value');
			chai.assert.equal(user_market_quote.bid_qty, market_quote_data.bid_qty, 'bid_qty property is equal to passed bid_qty value');
			chai.assert.equal(user_market_quote.bid, market_quote_data.bid, 'bid property is equal to passed bid value');
			chai.assert.equal(user_market_quote.offer, market_quote_data.offer, 'offer property is equal to passed offer value');
			chai.assert.equal(user_market_quote.offer_qty, market_quote_data.offer_qty, 'offer_qty property is equal to passed offer_qty value');
		});
	});

	describe('Getters and Setters', () => {

		it('Test Parent Getter and Setter', () => {
			let user_market_request = new UserMarketRequest({id:"1"});
			chai.assert.notDeepEqual(user_market_quote.getMarketRequest(), user_market_request, 'getMarketRequest() NOT be equal to the Parent set');
			user_market_quote.setMarketRequest(user_market_request);
			chai.assert.deepEqual(user_market_quote.getMarketRequest(), user_market_request, 'getMarketRequest() be equal to the Parent set');
		});
	});

	describe('toJSON', () => {

		it('return json structure with circular references removed', () => {
			try {
				JSON.stringify(user_market_quote);
			}
			catch(e) {
				console.log(e);
				chai.assert(false, "JSON.stringify(user_market_quote) should not throw and error");
			}
			chai.assert.isString(JSON.stringify(user_market_quote), 'Should be a string');
			chai.assert.notInclude(JSON.stringify(user_market_quote), '"_market"', 'Should not include _market key');
			chai.assert.notInclude(JSON.stringify(user_market_quote), '"_user_market_request"', 'Should not include _user_market_request key');

		});
	});
});