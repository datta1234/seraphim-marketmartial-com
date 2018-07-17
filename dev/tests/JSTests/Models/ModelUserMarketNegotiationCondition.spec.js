import UserMarketNegotiation from '../../../resources/assets/js/lib/UserMarketNegotiation.js';
import UserMarketNegotiationCondition from '../../../resources/assets/js/lib/UserMarketNegotiationCondition.js';

describe('class UserMarketNegotiationCondition', () => {

	let user_market_negotiation_condition = null;
	let user_market_negotiation_condition_data = {
		id: 2,
        title: "test title",
        alias: "test alias",
    	created_at: moment('1969-07-20 00:20:18')
	};

	beforeEach(function() {
    	user_market_negotiation_condition = new UserMarketNegotiationCondition(user_market_negotiation_condition_data);
  	});


	describe('UserMarketNegotiationCondition constructor', () => {

		it('UserMarketNegotiationCondition constructed with defaults', () => {
			let default_user_market_negotiation_condition = new UserMarketNegotiationCondition();
			chai.assert.equal(default_user_market_negotiation_condition.id, '','id property is default value');
			chai.assert.equal(default_user_market_negotiation_condition.title, '','title property is default value');
			chai.assert.equal(default_user_market_negotiation_condition.alias, '','alias property is default value');
			chai.assert(moment.isMoment(default_user_market_negotiation_condition.created_at),'created_at should be of type moment');
		});

		it('UserMarketNegotiationCondition constructed with passed params', () => {
			chai.assert.equal(user_market_negotiation_condition.id, user_market_negotiation_condition_data.id,'id property is equal to passed id value');
			chai.assert.equal(user_market_negotiation_condition.title, user_market_negotiation_condition_data.title,'title property is equal to passed title value');
			chai.assert.equal(user_market_negotiation_condition.alias, user_market_negotiation_condition_data.alias,'alias property is equal to passed alias value');
			chai.assert(user_market_negotiation_condition.created_at.isSame(user_market_negotiation_condition_data.created_at),'created_at property is equal to passed created_at value');
		});
	});

	describe('Getters and Setters', () => {

		it('Test UserMarketNegotiation Getter and Setter', () => {
			let user_market_negotiation = new UserMarketNegotiation({id:"1"});
			chai.assert.notDeepEqual(user_market_negotiation_condition.getUserMarketNegotiation(), user_market_negotiation, 'getUserMarketNegotiation() NOT be equal to the Parent set');
			user_market_negotiation_condition.setUserMarketNegotiation(user_market_negotiation);
			chai.assert.deepEqual(user_market_negotiation_condition.getUserMarketNegotiation(), user_market_negotiation, 'getUserMarketNegotiation() be equal to the Parent set');
		});
	});

	describe('toJSON', () => {

		it('return json structure with circular references removed', () => {
			try {
				JSON.stringify(user_market_negotiation_condition);
			}
			catch(e) {
				console.log(e);
				chai.assert(false, "JSON.stringify(user_market_negotiation_condition) should not throw and error");
			}
			chai.assert.isString(JSON.stringify(user_market_negotiation_condition), 'Should be a string');
			chai.assert.notInclude(JSON.stringify(user_market_negotiation_condition), '"_user_market_negotiation"', 'Should not include _user_market_negotiation key');
			chai.assert.notInclude(JSON.stringify(user_market_negotiation_condition), '"_user_market"', 'Should not include _user_market key');

		});
	});

	describe('Test storing User Market Negotiation Condition', () => {
		
		it('Pepare User Market Negotiation Condition object to store', () => {
			let prepared_object = user_market_negotiation_condition.prepareStore();
			let test_keys = ['id','title','alias'];
			chai.assert.containsAllKeys(prepared_object,test_keys,'The prepared User Market Negotiation Condition object contains all the keys needed to store the object')
			test_keys.forEach( (element) => {
				chai.assert.equal(user_market_negotiation_condition[element], prepared_object[element],'The User Market Negotiation Condition '+element+' is equal to the prepared object '+element);
			});
		});
	});
});