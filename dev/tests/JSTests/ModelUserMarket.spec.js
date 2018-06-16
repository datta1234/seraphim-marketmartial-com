import UserMarket from '../../resources/assets/js/lib/UserMarket.js';
import UserMarketNegotiation from '../../resources/assets/js/lib/UserMarketNegotiation.js';
import UserMarketRequest from '../../resources/assets/js/lib/UserMarketRequest.js';

describe('class UserMarket', () => {

	let user_market = null;
	let user_market_negotiation_array = [
    	new UserMarketNegotiation({id: "1"}),
    	new UserMarketNegotiation({id: "2"}),
    	new UserMarketNegotiation({id: "3"})
    ];
	let user_market_data = {
		id: 2,
        status: "test status",
        current_market_negotiation: user_market_negotiation_array[0],
        created_at: moment('1969-07-20 00:20:18'),
        market_negotiations: user_market_negotiation_array
	};

	beforeEach(function() {
    	user_market = new UserMarket(user_market_data);
  	});


	describe('UserMarket constructor', () => {

		it('UserMarket constructed with defaults', () => {
			let default_user_market = new UserMarket();
			chai.assert(default_user_market.id == '','id property is default value');
			chai.assert(default_user_market.status == '','status property is default value');
			chai.assert(default_user_market.current_market_negotiation == null,'current_market_negotiation property is default value');
			chai.assert(moment.isMoment(default_user_market.created_at),'created_at should be of type moment');
			chai.assert.lengthOf(default_user_market.market_negotiations, 0, 'market_negotiations array is empty');
		});

		it('UserMarket constructed with passed params', () => {
			chai.assert(user_market.id == user_market_data.id,'id property is equal to passed id value');
			chai.assert(user_market.status == user_market_data.status,'status property is equal to passed status value');
			chai.assert(user_market.created_at.isSame(user_market_data.created_at),'created_at property is equal to passed created_at value');
			chai.assert.deepEqual(user_market.current_market_negotiation, user_market_data.current_market_negotiation, 'current_market_negotiation property is equal to passed current_market_negotiation array');
			chai.assert.deepEqual(user_market.market_negotiations, user_market_data.market_negotiations, 'market_negotiations property is equal to passed market_negotiations array');
		});
	});

	describe('Getters and Setters', () => {

		it('Test Parent Getter and Setter', () => {
			let parent_market_request = new UserMarketRequest({id:"1"});
			chai.assert.notDeepEqual(user_market.getParent(), parent_market_request, 'getParent() NOT be equal to the Parent set');
			user_market.setParent(parent_market_request);
			chai.assert.deepEqual(user_market.getParent(), parent_market_request, 'getParent() be equal to the Parent set');
		});

		it('Test CurrentNegotiation Getter and Setter', () => {
			let user_market_negotiation = new UserMarketNegotiation({id:"4"});
			chai.assert.notDeepEqual(user_market.getCurrentNegotiation(), user_market_negotiation, 'getCurrentNegotiation() NOT be equal to the user_market_negotiation set');
			user_market.setCurrentNegotiation(user_market_negotiation);
			chai.assert.deepEqual(user_market.getCurrentNegotiation(), user_market_negotiation, 'getCurrentNegotiation() be equal to the user_market_negotiation set');
		});
	});

	describe('Add new Negotiations', () => {
		
		it('New single Negotiation added', () => {
			let user_market_negotiation = new UserMarketNegotiation({id: "4"});
			user_market.addNegotiation(user_market_negotiation);
			chai.assert.deepInclude(user_market.market_negotiations, user_market_negotiation, 'new UserMarketNegotiation user_market_negotiation should be in user_market.market_negotiations');
		});

		it('New array of Negotiations added', () => {
			let user_market_negotiations = [
				new UserMarketNegotiation({id: "5"}),
				new UserMarketNegotiation({id: "6"}),
				new UserMarketNegotiation({id: "7"})
			];
			user_market.addNegotiations(user_market_negotiations);
			chai.assert.deepInclude(user_market.market_negotiations, user_market_negotiations[0], 'new UserMarketNegotiation user_market_negotiations[0] should be in user_market.market_negotiations');
			chai.assert.deepInclude(user_market.market_negotiations, user_market_negotiations[1], 'new UserMarketNegotiation user_market_negotiations[1] should be in user_market.market_negotiations');
			chai.assert.deepInclude(user_market.market_negotiations, user_market_negotiations[2], 'new UserMarketNegotiation user_market_negotiations[1] should be in user_market.market_negotiations');
		});
	});

	describe('toJSON', () => {

		it('return json structure with circular references removed', () => {
			try {
				JSON.stringify(user_market);
			}
			catch(e) {
				console.log(e);
				chai.assert(false, "JSON.stringify(user_market) should not throw and error");
			}
			chai.assert.isString(JSON.stringify(user_market), 'Should be a string');
			chai.assert.notInclude(JSON.stringify(user_market), '"_user_market_request"', 'Should not include _user_market_request key');
			chai.assert.notInclude(JSON.stringify(user_market), '"_user_market"', 'Should not include _user_market key');

		});
	});

});