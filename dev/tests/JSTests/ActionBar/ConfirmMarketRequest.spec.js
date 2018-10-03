import { mount } from '@vue/test-utils';
import ConfirmMarketRequest from '../../../resources/assets/js/components/ActionBar/Components/RequestMarket/Components/ConfirmMarketRequestComponent.vue';
import Market from '../../../resources/assets/js/lib/Market.js';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

// create an extended `Vue` constructor
const localVue = createLocalVue();
// install boostrap to this vue instance only
localVue.use(BootstrapVue);

describe('ConfirmMarketRequestComponent.vue', () => {
	let test_markets = [
		new Market({id:1,title:"TOP40",market_type_id:1,is_displayed:1}),
		new Market({id:2,title:"DTOP",market_type_id:1,is_displayed:1}),
		new Market({id:3,title:"DCAP",market_type_id:1,is_displayed:1})
	];
	let index_data = {
        market_type_title:'Index Option',
        market_type: {
        	id: 1,
			title: "Index Option",
			markets: test_markets
		},
        index_market_object: {
            market:test_markets[0],
            trade_structure: 'Outright',
            trade_structure_groups: [],
            expiry_dates:["2019-09-19 00:00:00"],
            details: {
		      	fields: [
			        {
			          is_selected: true,
			          strike: 455486,
			          quantity: 500
			        }
		      	]
		    }
        },
        number_of_dates: 1,
    };

	it('Date format casting', () => {
		const confirmDetailsDateWrapper = mount(ConfirmMarketRequest, {
			propsData: {
				callback: () => {},
				data: index_data,
				errors:{
					messages:[]
				}
			},
			localVue
		});
		chai.assert( confirmDetailsDateWrapper.vm.castToMoment(index_data.index_market_object.expiry_dates[0]) == "Sep19", "The passed date (2019-09-19 00:00:00) was casted to format MMMYY (Sep19)");
	});

	it('Confirm Details', (done) => {
		let confirmDetailsCallback = () => {
			done();
		};

		const confirmDetailsWrapper = mount(ConfirmMarketRequest, {
			propsData: {
				callback: confirmDetailsCallback,
				data: index_data,
				errors:{
					messages:[]
				}
			},
			localVue
		});
		confirmDetailsWrapper.find('#confirm-request-market').trigger('click');

	});
});