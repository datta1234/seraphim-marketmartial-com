import Market from '../../resources/assets/js/lib/Market.js';
import UserMarketRequest from '../../resources/assets/js/lib/UserMarketRequest.js';

describe('class Market', () => {

	let market = null;

	let market_data = {
		id: 2,
        title: "Test title",
        description: "Test discription",
        order: 1,
        market_type_id: 2,
        market_requests: [
			new UserMarketRequest({id: "1"}),
			new UserMarketRequest({id: "2"}),
			new UserMarketRequest({id: "3"})
		],
	};

	beforeEach(function() {
    	market = new Market(market_data);	
  	});

	describe('Market constructor', () => {
		
		it('Market constructed with defaults', () => {
			let default_market = new Market();
			chai.assert(default_market.id == '','id property is default value');
			chai.assert(default_market.title  == '','title property is default value');
			chai.assert(default_market.description  == '' ,'description property is default value');
			chai.assert(default_market.order  == '' ,'order property is default value');
			chai.assert(default_market.market_type_id  == '' ,'market_type_id property is default value');
			chai.assert.lengthOf(default_market.market_requests, 0, 'market_requests array is empty');
		});

		it('Market constructed with passed params', () => {
			chai.assert(market.id == market_data.id,'id property is equal to passed id');
			chai.assert(market.title  == market_data.title,'title property is equal to passed title');
			chai.assert(market.description  == market_data.description,'description property is equal to passed description');
			chai.assert(market.order  == market_data.order,'order property is equal to passed order');
			chai.assert(market.market_type_id  == market_data.market_type_id,'market_type_id property is equal to passed market_type_id');
			chai.assert.deepEqual(market.market_requests, market_data.market_requests, 'market_request property is equal to passed market market_request array');

		});
	});

	describe('Adding new Market Requests', () => {
		
		it('New single Market Request added', () => {
			let market_request = new UserMarketRequest({id: "4"});
			market.addMarketRequest(market_request);
			chai.assert.deepInclude(market.market_requests, market_request, 'new UserMarketRequest market_request should be in market.market_requests');
		});

		it('New array of Market Requests added', () => {
			let market_requests = [
				new UserMarketRequest({id: "5"}),
				new UserMarketRequest({id: "6"}),
				new UserMarketRequest({id: "7"})
			];
			market.addMarketRequests(market_requests);
			chai.assert.deepInclude(market.market_requests, market_requests[0], 'new UserMarketRequest market_requests[0] should be in market.market_requests');
			chai.assert.deepInclude(market.market_requests, market_requests[1], 'new UserMarketRequest market_requests[1] should be in market.market_requests');
			chai.assert.deepInclude(market.market_requests, market_requests[2], 'new UserMarketRequest market_requests[2] should be in market.market_requests');
		});
	});

	describe('toJSON', () => {

		it('return json structure with circular references removed', () => {
			try {
				JSON.stringify(market);
			}
			catch(e) {
				console.log(e);
				chai.assert(false, "JSON.stringify(market) should not throw and error");
			}
			chai.assert.isString(JSON.stringify(market), 'Should be a string');
			chai.assert.notInclude(JSON.stringify(market), '"_market"', 'Should not include _market key');

		});
	});
});