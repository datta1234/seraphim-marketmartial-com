import UserMarket from '../../../resources/assets/js/lib/UserMarket.js';
import UserMarketNegotiation from '../../../resources/assets/js/lib/UserMarketNegotiation.js';
import UserMarketNegotiationCondition from '../../../resources/assets/js/lib/UserMarketNegotiationCondition.js';

describe('class UserMarketNegotiation', () => {

	let user_market_negotiation = null;
	let conditions = [
    	new UserMarketNegotiationCondition({id: "1"}),
    	new UserMarketNegotiationCondition({id: "2"}),
    	new UserMarketNegotiationCondition({id: "3"})
    ];
	let user_market_negotiation_data = {
		id: 2,
		bid: "Test bid",
        offer: "Test offer",
        bid_qty: 2,
        offer_qty: 3,
        bid_premium: "Test bid premium",
        offer_premium: "Test offer premium",
        is_put: true,
        status: "Test status",
        created_at: moment('1969-07-20 00:20:18'),
        user_market_negotiation_condition: conditions
	};

	beforeEach(function() {
    	user_market_negotiation = new UserMarketNegotiation(user_market_negotiation_data);
  	});


	describe('UserMarketNegotiation constructor', () => {

		it('UserMarketNegotiation constructed with defaults', () => {
			let default_user_market_negotiation = new UserMarketNegotiation();
			chai.assert(default_user_market_negotiation.id == '','id property is default value');
			chai.assert(default_user_market_negotiation.bid == '','bid property is default value');
			chai.assert(default_user_market_negotiation.offer == '','offer property is default value');
			chai.assert(default_user_market_negotiation.bid_qty == 0,'bid_qty property is default value');
			chai.assert(default_user_market_negotiation.offer_qty == 0,'offer_qty property is default value');
			chai.assert(default_user_market_negotiation.bid_premium == '','bid_premium property is default value');
			chai.assert(default_user_market_negotiation.offer_premium == '','offer_premium property is default value');
			chai.assert(default_user_market_negotiation.is_put == false,'is_put property is default value');
			chai.assert(default_user_market_negotiation.status == '','status property is default value');
			chai.assert(moment.isMoment(default_user_market_negotiation.created_at),'created_at should be of type moment');
			chai.assert.lengthOf(default_user_market_negotiation.conditions, 0, 'conditions array is empty');
		});

		it('UserMarketNegotiation constructed with passed params', () => {
			chai.assert(user_market_negotiation.id == user_market_negotiation_data.id,'id property is equal to passed id value');
			chai.assert(user_market_negotiation.bid == user_market_negotiation_data.bid,'bid property is equal to passed bid value');
			chai.assert(user_market_negotiation.offer == user_market_negotiation_data.offer,'offer property is equal to passed offer value');
			chai.assert(user_market_negotiation.bid_qty == user_market_negotiation_data.bid_qty,'bid_qty property is equal to passed bid_qty value');
			chai.assert(user_market_negotiation.offer_qty == user_market_negotiation_data.offer_qty,'offer_qty property is equal to passed offer_qty value');
			chai.assert(user_market_negotiation.bid_premium == user_market_negotiation_data.bid_premium,'bid_premium property is equal to passed bid_premium value');
			chai.assert(user_market_negotiation.offer_premium == user_market_negotiation_data.offer_premium,'offer_premium property is equal to passed offer_premium value');
			chai.assert(user_market_negotiation.is_put == user_market_negotiation_data.is_put,'is_put property is equal to passed is_put value');
			chai.assert(user_market_negotiation.status == user_market_negotiation_data.status,'status property is equal to passed status value');
			
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
			chai.assert.deepEqual
		});
	});
});