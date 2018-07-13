import UserMarketRequest from '../../../resources/assets/js/lib/UserMarketRequest.js';
import UserMarketQuote from '../../../resources/assets/js/lib/UserMarketQuote.js';
import UserMarket from '../../../resources/assets/js/lib/UserMarket.js';
import Market from '../../../resources/assets/js/lib/Market.js';

describe('class UserMarketRequest', () => {

	let user_market_request = null;
	let user_market_quotes_array = [
    	new UserMarketQuote({id: "1"}),
    	new UserMarketQuote({id: "2"}),
    	new UserMarketQuote({id: "3"})
    ];
	let market_request_data = {
		id: 2,
        trade_structure: "Test title",
        attributes: {
	        state: "test state",
	        bid_state: "test bid state",
	        offer_state: " test offer state",
        },
        created_at: moment('1969-07-20 00:20:18'),
        trade_items:[{
	        id: 1,
	        title: "Expiration Date",
	        value: moment('1969-07-20 00:20:18')
	    },{
	        id: 2,
	        title: "Expiration Date",
	        value: moment('1969-07-20 00:20:18')
	    }],
        user_market_quotes: user_market_quotes_array,
        quote: user_market_quotes_array[0],
        chosen_user_market: new UserMarket({id: "1"}),
	};

	beforeEach(function() {
    	user_market_request = new UserMarketRequest(market_request_data);	
  	});


	describe('UserMarketRequest constructor', () => {

		it('UserMarketRequest constructed with defaults', () => {
			let default_user_market_request = new UserMarketRequest();
			chai.assert(default_user_market_request.id == '','id property is default value');
			chai.assert(default_user_market_request.trade_structure == '','trade_structure property is default value');
			chai.assert(default_user_market_request.attributes.state == '','attributes.state property is default value');
			chai.assert(default_user_market_request.attributes.bid_state == '','attributes.bid_state property is default value');
			chai.assert(default_user_market_request.attributes.offer_state == '','attributes.offer_state property is default value');
			chai.assert(default_user_market_request.quote == null,'quote property is default value');
			chai.assert(default_user_market_request.chosen_user_market == null,'chosen_user_market property is default value');
			chai.assert(moment.isMoment(default_user_market_request.created_at),'created_at should be of type moment');
			chai.assert.lengthOf(default_user_market_request.trade_items, 0, 'trade_items array is empty');
			chai.assert.lengthOf(default_user_market_request.quotes, 0, 'quotes array is empty');
		});

		it('UserMarketRequest constructed with passed params', () => {
			chai.assert(user_market_request.id == market_request_data.id,'id property is equal to passed id value');
			chai.assert(user_market_request.trade_structure == market_request_data.trade_structure,'trade_structure property is equal to passed trade_structure value');
			chai.assert(user_market_request.attributes.state == market_request_data.attributes.state,'attributes.state property is equal to passed attributes.state value');
			chai.assert(user_market_request.attributes.bid_state == market_request_data.attributes.bid_state,'attributes.bid_state property is equal to passed attributes.bid_state value');
			chai.assert(user_market_request.attributes.offer_state == market_request_data.attributes.offer_state,'attributes.offer_state property is equal to passed attributes.offer_state value');
			chai.assert(user_market_request.chosen_user_market == market_request_data.chosen_user_market,'chosen_user_market property is equal to passed chosen_user_market value');
			chai.assert(user_market_request.created_at.isSame(market_request_data.created_at),'created_at property is equal to passed created_at value');
			chai.assert.deepEqual(user_market_request.quote, market_request_data.quote,'quote property is equal to passed quote value');
			chai.assert.deepEqual(user_market_request.quotes, market_request_data.user_market_quotes, 'quotes property is equal to passed quotes array');
			chai.assert.deepEqual(user_market_request.trade_items, market_request_data.trade_items, 'trade_items property is equal to passed trade_items array');

		});
	});

	describe('Add new User Quotes', () => {

		it('New single User Quote added', () => {
			let user_market_quote = new UserMarketQuote({id: "4"});
			user_market_request.addUserMarketQuote(user_market_quote);
			chai.assert.deepInclude(user_market_request.quotes, user_market_quote, 'new UserMarketQuote user_market_quote should be in user_market_request.quotes');
		});

		it('New array of User Quotes added', () => {
			let user_market_quotes = [
				new UserMarketQuote({id: "5"}),
				new UserMarketQuote({id: "6"}),
				new UserMarketQuote({id: "7"})
			];
			user_market_request.addUserMarketQuotes(user_market_quotes);
			chai.assert.deepInclude(user_market_request.quotes, user_market_quotes[0], 'new UserMarketQuote user_market_quotes[0] should be in user_market_request.quotes');
			chai.assert.deepInclude(user_market_request.quotes, user_market_quotes[1], 'new UserMarketQuote user_market_quotes[1] should be in user_market_request.quotes');
			chai.assert.deepInclude(user_market_request.quotes, user_market_quotes[2], 'new UserMarketQuote user_market_quotes[2] should be in user_market_request.quotes');
		});
	});

	describe('Getters and Setters', () => {

		it('Test Market Getter and Setter', () => {
			let test_market = new Market({id:"1"});
			chai.assert.notDeepEqual(user_market_request.getMarket(), test_market, 'getMarket() NOT be equal to the Market set');
			user_market_request.setMarket(test_market);
			chai.assert.deepEqual(user_market_request.getMarket(), test_market, 'getMarket() be equal to the Market set');
		});

		it('Test Chosen User Market Getter and Setter', () => {
			let user_market = new UserMarket({id:"2"});
			chai.assert.notDeepEqual(user_market_request.getChosenUserMarket(), user_market, 'getChosenUserMarket() NOT be equal to the user_market set');
			user_market_request.setChosenUserMarket(user_market);
			chai.assert.deepEqual(user_market_request.getChosenUserMarket(), user_market, 'getChosenUserMarket() be equal to the user_market set');
		});

		it('Test Chosen User Market Quote Setter', () => {
			chai.assert(false, "TODO");			
		});
	});

	describe('Add new Trade Items', () => {
		
		it('New single Trade Item added', () => {
			let trade_item = {
		        id: 3,
		        title: "Expiration Date",
		        value: moment('1969-07-20 00:20:18')
		    };
			user_market_request.addTradeItem(trade_item);
			chai.assert.deepInclude(user_market_request.trade_items, trade_item, 'new trade item trade_item should be in user_market_request.trade_items');
		});

		it('New array of Trade Items added', () => {
			let trade_items = [{
		        id: 4,
		        title: "Expiration Date",
		        value: moment('1969-07-20 00:20:18')
		    },{
		        id: 5,
		        title: "Expiration Date",
		        value: moment('1969-07-20 00:20:18')
		    }];
			user_market_request.addTradeItems(trade_items);
			chai.assert.deepInclude(user_market_request.trade_items, trade_items[0], 'new UserMarketRequest trade_items[0] should be in user_market_request.trade_items');
			chai.assert.deepInclude(user_market_request.trade_items, trade_items[1], 'new UserMarketRequest trade_items[1] should be in user_market_request.trade_items');
		});
	});

	describe('toJSON', () => {

		it('return json structure with circular references removed', () => {
			try {
				JSON.stringify(user_market_request);
			}
			catch(e) {
				console.log(e);
				chai.assert(false, "JSON.stringify(user_market_request) should not throw and error");
			}
			chai.assert.isString(JSON.stringify(user_market_request), 'Should be a string');
			chai.assert.notInclude(JSON.stringify(user_market_request), '"_market"', 'Should not include _market key');
			chai.assert.notInclude(JSON.stringify(user_market_request), '"_user_market_request"', 'Should not include _user_market_request key');

		});
	});
});