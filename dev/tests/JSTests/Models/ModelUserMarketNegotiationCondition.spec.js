import UserMarketNegotiation from '../../../resources/assets/js/lib/UserMarketNegotiation.js';
import UserMarketNegotiationCondition from '../../../resources/assets/js/lib/UserMarketNegotiationCondition.js';

describe('class UserMarketNegotiationCondition', () => {

	let user_market_negotiation_condition = null;
	let user_market_negotiation_condition_data = {
		id: 2,
        title: "test title",
        options: {
	        timeout: true,
	        sell: true,
	        buy: false,
	    },
    	created_at: moment('1969-07-20 00:20:18')
	};

	beforeEach(function() {
    	user_market_negotiation_condition = new UserMarketNegotiationCondition(user_market_negotiation_condition_data);
  	});


	describe('UserMarketNegotiationCondition constructor', () => {

		it('UserMarketNegotiationCondition constructed with defaults', () => {
			let default_user_market_negotiation_condition = new UserMarketNegotiationCondition();
			chai.assert(default_user_market_negotiation_condition.id == '','id property is default value');
			chai.assert(default_user_market_negotiation_condition.title == '','title property is default value');
			chai.assert(default_user_market_negotiation_condition.options.timeout == false,'options.timeout property is default value');
			chai.assert(default_user_market_negotiation_condition.options.sell == false,'options.sell property is default value');
			chai.assert(default_user_market_negotiation_condition.options.buy == false,'options.buy property is default value');
			chai.assert(moment.isMoment(default_user_market_negotiation_condition.created_at),'created_at should be of type moment');
		});

		it('UserMarketNegotiationCondition constructed with passed params', () => {
			chai.assert(user_market_negotiation_condition.id == user_market_negotiation_condition_data.id,'id property is equal to passed id value');
			chai.assert(user_market_negotiation_condition.title == user_market_negotiation_condition_data.title,'title property is equal to passed title value');
			chai.assert(user_market_negotiation_condition.options.timeout == user_market_negotiation_condition_data.options.timeout,'options.timeout property is equal to passed options.timeout value');
			chai.assert(user_market_negotiation_condition.options.sell == user_market_negotiation_condition_data.options.sell,'options.sell property is equal to passed options.sell value');
			chai.assert(user_market_negotiation_condition.options.buy == user_market_negotiation_condition_data.options.buy,'options.buy property is equal to passed options.buy value');
			chai.assert(user_market_negotiation_condition.created_at.isSame(user_market_negotiation_condition_data.created_at),'created_at property is equal to passed created_at value');
		});
	});

	describe('Getters and Setters', () => {

		it('Test Parent Getter and Setter', () => {
			let parent_user_market_negotiation = new UserMarketNegotiation({id:"1"});
			chai.assert.notDeepEqual(user_market_negotiation_condition.getParent(), parent_user_market_negotiation, 'getParent() NOT be equal to the Parent set');
			user_market_negotiation_condition.setParent(parent_user_market_negotiation);
			chai.assert.deepEqual(user_market_negotiation_condition.getParent(), parent_user_market_negotiation, 'getParent() be equal to the Parent set');
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
});