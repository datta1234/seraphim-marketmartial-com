import UserMarket from '../../../resources/assets/js/lib/UserMarket.js';
import UserMarketNegotiation from '../../../resources/assets/js/lib/UserMarketNegotiation.js';
import UserMarketRequest from '../../../resources/assets/js/lib/UserMarketRequest.js';
import UserMarketNegotiationCondition from '../../../resources/assets/js/lib/UserMarketNegotiationCondition.js';

describe('class UserMarket', () => {

	let user_market = null;
	let test_conditions = require(__dirname + '/mockData/UserMarketNegotiationConditions.json');
	let test_negotiations = require(__dirname + '/mockData/UserMarketNegotiations.json');
	let user_market_data = require(__dirname + '/mockData/UserMarket.json');

	test_negotiations.forEach( (element) => {
		let condition_array = []
		test_conditions.forEach((element) => {
			condition_array.push( new UserMarketNegotiationCondition(element) );
		});
		element.user_market_negotiation_condition = condition_array;
		user_market_data.market_negotiations.push( new UserMarketNegotiation(element) );	
	});

	user_market_data.current_market_negotiation = user_market_data.market_negotiations[0];

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
			chai.assert(moment.isMoment(default_user_market.updated_at),'updated_at should be of type moment');
			chai.assert.lengthOf(default_user_market.market_negotiations, 0, 'market_negotiations array is empty');
		});

		it('UserMarket constructed with passed params', () => {
			chai.assert(user_market.id == user_market_data.id,'id property is equal to passed id value');
			chai.assert(user_market.status == user_market_data.status,'status property is equal to passed status value');
			chai.assert(user_market.created_at.isSame(user_market_data.created_at),'created_at property is equal to passed created_at value');
			chai.assert(user_market.updated_at.isSame(user_market_data.updated_at),'updated_at property is equal to passed updated_at value');
			chai.assert.deepEqual(user_market.current_market_negotiation, user_market_data.current_market_negotiation, 'current_market_negotiation property is equal to passed current_market_negotiation array');
			chai.assert.deepEqual(user_market.market_negotiations, user_market_data.market_negotiations, 'market_negotiations property is equal to passed market_negotiations array');
		});
	});

	describe('Getters and Setters', () => {

		it('Test Market Request Getter and Setter', () => {
			let parent_market_request = new UserMarketRequest({id:"1"});
			chai.assert.notDeepEqual(user_market.getMarketRequest(), parent_market_request, 'getMarketRequest() NOT be equal to the Parent set');
			user_market.setMarketRequest(parent_market_request);
			chai.assert.deepEqual(user_market.getMarketRequest(), parent_market_request, 'getMarketRequest() be equal to the Parent set');
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
			//TODO - move test data out to json file
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


	describe('Test storing User Market ', () => {
		
		it('Pepare user market object to store', () => {
			let test_store_data = require(__dirname + '/mockData/UserMarketStoreObject.json');
			chai.assert.deepEqual(user_market.prepareStore(), test_store_data, "The User Market store object is equal to the set object");
		});

		it('Store user market', (done) => {
			
			let api_store_response = nock(axios.defaults.baseUrl)
			.defaultReplyHeaders({ 'access-control-allow-origin': '*' })
			.post('/trade/market-request/2/user-market', user_market.prepareStore())
			.reply(200, function(){
				done();
			})
			.persist();//always handles

			user_market.store();
		});
	});

});