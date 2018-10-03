import UserMarket from '../../../resources/assets/js/lib/UserMarket.js';
import UserMarketRequest from '../../../resources/assets/js/lib/UserMarketRequest.js';
import UserMarketNegotiation from '../../../resources/assets/js/lib/UserMarketNegotiation.js';
import UserMarketNegotiationCondition from '../../../resources/assets/js/lib/UserMarketNegotiationCondition.js';

describe('class UserMarketNegotiation', () => {
	//TODO - move test data out to json file
	let user_market_negotiation = null;
	let test_conditions = [
    	new UserMarketNegotiationCondition({id: "1", title: "test title 1", alias: "test_alias_1"}),
    	new UserMarketNegotiationCondition({id: "2", title: "test title 2", alias: "test_alias_2"}),
    	new UserMarketNegotiationCondition({id: "3", title: "test title 3", alias: "test_alias_3"})
    ];
	let user_market_negotiation_data = {
		id: 2,
		bid: "Test bid",
        offer: "Test offer",
        bid_qty: 2,
        offer_qty: 3,
        is_repeat: true,
        has_premium_calc: true,
        bid_premium: "Test bid premium",
        offer_premium: "Test offer premium",
        is_put: true,
        status: "Test status",
        created_at: moment('1969-07-20 00:20:18'),
        user_market_negotiation_condition: test_conditions
	};

	beforeEach(function() {
    	user_market_negotiation = new UserMarketNegotiation(user_market_negotiation_data);
  	});


	describe('UserMarketNegotiation constructor', () => {

		it('UserMarketNegotiation constructed with defaults', () => {
			let default_user_market_negotiation = new UserMarketNegotiation();
			chai.assert.equal(default_user_market_negotiation.id, '', 'id property is default value');
			chai.assert.equal(default_user_market_negotiation.bid, '', 'bid property is default value');
			chai.assert.equal(default_user_market_negotiation.offer, '', 'offer property is default value');
			chai.assert.equal(default_user_market_negotiation.bid_qty, 500, 'bid_qty property is default value');
			chai.assert.equal(default_user_market_negotiation.offer_qty, 500, 'offer_qty property is default value');
			chai.assert.isFalse(default_user_market_negotiation.is_repeat, 'is_repeat property is default value');
			chai.assert.isFalse(default_user_market_negotiation.has_premium_calc, 'has_premium_calc property is default value');
			chai.assert.equal(default_user_market_negotiation.bid_premium, '', 'bid_premium property is default value');
			chai.assert.equal(default_user_market_negotiation.offer_premium, '', 'offer_premium property is default value');
			chai.assert.isFalse(default_user_market_negotiation.is_put, 'is_put property is default value');
			chai.assert.equal(default_user_market_negotiation.status, '', 'status property is default value');
			chai.assert(moment.isMoment(default_user_market_negotiation.created_at),'created_at should be of type moment');
			chai.assert.lengthOf(default_user_market_negotiation.conditions, 0, 'conditions array is empty');
		});

		it('UserMarketNegotiation constructed with passed params', () => {
			chai.assert.equal(user_market_negotiation.id, user_market_negotiation_data.id,'id property is equal to passed id value');
			chai.assert.equal(user_market_negotiation.bid, user_market_negotiation_data.bid,'bid property is equal to passed bid value');
			chai.assert.equal(user_market_negotiation.offer, user_market_negotiation_data.offer,'offer property is equal to passed offer value');
			chai.assert.equal(user_market_negotiation.bid_qty, user_market_negotiation_data.bid_qty,'bid_qty property is equal to passed bid_qty value');
			chai.assert.equal(user_market_negotiation.offer_qty, user_market_negotiation_data.offer_qty,'offer_qty property is equal to passed offer_qty value');
			chai.assert.equal(user_market_negotiation.is_repeat, user_market_negotiation_data.is_repeat,'is_repeat property is equal to passed is_repeat value');
			chai.assert.equal(user_market_negotiation.has_premium_calc, user_market_negotiation_data.has_premium_calc,'has_premium_calc property is equal to passed has_premium_calc value');
			chai.assert.equal(user_market_negotiation.bid_premium, user_market_negotiation_data.bid_premium,'bid_premium property is equal to passed bid_premium value');
			chai.assert.equal(user_market_negotiation.offer_premium, user_market_negotiation_data.offer_premium,'offer_premium property is equal to passed offer_premium value');
			chai.assert.equal(user_market_negotiation.is_put, user_market_negotiation_data.is_put,'is_put property is equal to passed is_put value');
			chai.assert.equal(user_market_negotiation.status, user_market_negotiation_data.status,'status property is equal to passed status value');
			
			chai.assert(user_market_negotiation.created_at.isSame(user_market_negotiation_data.created_at),'created_at property is equal to passed created_at value');
			chai.assert.deepEqual(user_market_negotiation.conditions, user_market_negotiation_data.user_market_negotiation_condition, 'market_negotiations property is equal to passed market_negotiations array');
		});
	});

	describe('Getters and Setters', () => {

		it('Test User Market Getter and Setter', () => {
			let parent_user_market = new UserMarket({id:"1"});
			chai.assert.notDeepEqual(user_market_negotiation.getUserMarket(), parent_user_market, 'getUserMarket() NOT be equal to the Parent set');
			user_market_negotiation.setUserMarket(parent_user_market);
			chai.assert.deepEqual(user_market_negotiation.getUserMarket(), parent_user_market, 'getUserMarket() be equal to the Parent set');
		});
	});

	describe('Add new Negotiations Condition', () => {
			
		it('New single Negotiation Condition added', () => {
			let user_market_negotiation_condition = new UserMarketNegotiationCondition({id: "4"});
			user_market_negotiation.addUserMarketNegotiationCondition(user_market_negotiation_condition);
			chai.assert.deepInclude(user_market_negotiation.conditions, user_market_negotiation_condition, 'new UserMarketNegotiationCondition user_market_negotiation_condition should be in user_market_negotiation.conditions');
		});

		it('New array of Negotiation Conditions added', () => {
			let user_market_negotiation_conditions = [
				new UserMarketNegotiationCondition({id: "5"}),
				new UserMarketNegotiationCondition({id: "6"}),
				new UserMarketNegotiationCondition({id: "7"})
			];
			user_market_negotiation.addUserMarketNegotiationConditions(user_market_negotiation_conditions);
			chai.assert.deepInclude(user_market_negotiation.conditions, user_market_negotiation_conditions[0], 'new UserMarketNegotiationCondition user_market_negotiation_conditions[0] should be in user_market_negotiation.conditions');
			chai.assert.deepInclude(user_market_negotiation.conditions, user_market_negotiation_conditions[1], 'new UserMarketNegotiationCondition user_market_negotiation_conditions[1] should be in user_market_negotiation.conditions');
			chai.assert.deepInclude(user_market_negotiation.conditions, user_market_negotiation_conditions[2], 'new UserMarketNegotiationCondition user_market_negotiation_conditions[1] should be in user_market_negotiation.conditions');
		});
	});

	describe('toJSON', () => {

		it('return json structure with circular references removed', () => {
			try {
				JSON.stringify(user_market_negotiation);
			}
			catch(e) {
				console.log(e);
				chai.assert(false, "JSON.stringify(user_market_negotiation) should not throw and error");
			}
			chai.assert.isString(JSON.stringify(user_market_negotiation), 'Should be a string');
			chai.assert.notInclude(JSON.stringify(user_market_negotiation), '"_market"', 'Should not include _market key');
			chai.assert.notInclude(JSON.stringify(user_market_negotiation), '"_user_market_request"', 'Should not include _user_market_request key');

		});
	});

	describe('Test storing User Market Negotiation ', () => {
		
		it('Pepare User Market Negotiation object to store', () => {

			let test_object = {
				bid: user_market_negotiation_data.bid,
	            offer: user_market_negotiation_data.offer,
	            bid_qty: user_market_negotiation_data.bid_qty,
	            offer_qty: user_market_negotiation_data.offer_qty,
	            is_repeat: user_market_negotiation_data.is_repeat,
	            has_premium_calc: user_market_negotiation_data.has_premium_calc,
	            bid_premium: user_market_negotiation_data.bid_premium,
	            offer_premium: user_market_negotiation_data.offer_premium,
	            conditions: [
	            	test_conditions[0].prepareStore(),
	            	test_conditions[1].prepareStore(),
	            	test_conditions[2].prepareStore()
	            ],
			};

			chai.assert.deepEqual(user_market_negotiation.prepareStore(), test_object, "The User Market Negotiation store object is equal to the set object");
		});
	});


	describe('Test patching the user market Negotiation ', () => {
		
		it.only('Pepare User Market Negotiation object to store', () => {
			//need a full negotitaion as the method will need to have the 
			let testUserMarketRequest = require(__dirname + '/mockData/UserMarketNegotiationPatchObject.json');
			let default_user_market_request = new UserMarket(testUserMarketRequest);
			let current_negotiation = default_user_market_request.getCurrentNegotiation();
			

			let api_store_response = nock(axios.defaults.baseUrl)
			
			//intercept the enpoint thats going to be hit
			.defaultReplyHeaders({ 'access-control-allow-origin': '*' })
			.post('/user-market/2/market_negotiation/2', current_negotiation.prepareStore())
			.reply(200, function(){
				done();
			})
			.persist();//always handles

			current_negotiation.patch();

		});
	});
});