import UserMarketQuote from '../../resources/assets/js/lib/UserMarketQuote.js';
import UserMarketRequest from '../../resources/assets/js/lib/UserMarketRequest.js';

describe('class UserMarketQuote', () => {

	let user_market_quote = null;
	let market_quote_data = {
		id: 2,
        bid_only: true,
        offer_only: false,
        vol_spread: "4",
        created_at: moment('1969-07-20 00:20:18'),
	};

	beforeEach(function() {
    	user_market_quote = new UserMarketQuote(market_quote_data);	
  	});


	describe('UserMarketQuote constructor', () => {

		it('UserMarketRequest constructed with defaults', () => {
			let default_user_user_market_quote = new UserMarketQuote();
			chai.assert(default_user_user_market_quote.id == '','id property is default value');
			chai.assert(default_user_user_market_quote.bid_only == false,'bid_only property is default value');
			chai.assert(default_user_user_market_quote.offer_only == false,'offer_only property is default value');
			chai.assert(default_user_user_market_quote.vol_spread == '','vol_spread_state property is default value');
			chai.assert(moment.isMoment(default_user_user_market_quote.created_at),'created_at should be of type moment');
		});

		it('UserMarketRequest constructed with passed params', () => {
			chai.assert(user_market_quote.id == market_quote_data.id,'id property is equal to passed id value');
			chai.assert(user_market_quote.bid_only == market_quote_data.bid_only,'bid_only property is equal to passed bid_only value');
			chai.assert(user_market_quote.offer_only == market_quote_data.offer_only,'offer_only property is equal to passed offer_only value');
			chai.assert(user_market_quote.vol_spread == market_quote_data.vol_spread,'vol_spread property is equal to passed vol_spread value');
			chai.assert(user_market_quote.created_at.isSame(market_quote_data.created_at),'created_at property is equal to passed created_at value');

		});
	});

	describe('Getters and Setters', () => {

		it('Test Parent Getter and Setter', () => {
			let parent_market_quote = new UserMarketRequest({id:"1"});
			chai.assert.notDeepEqual(user_market_quote.getParent(), parent_market_quote, 'getParent() NOT be equal to the Parent set');
			user_market_quote.setParent(parent_market_quote);
			chai.assert.deepEqual(user_market_quote.getParent(), parent_market_quote, 'getParent() be equal to the Parent set');
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