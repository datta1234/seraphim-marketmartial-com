import { mount } from '@vue/test-utils';
import Details from '../../../resources/assets/js/components/ActionBar/Components/RequestMarket/Components/DetailsComponent.vue';
import Market from '../../../resources/assets/js/lib/Market.js';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

// create an extended `Vue` constructor
const localVue = createLocalVue();
// install boostrap to this vue instance only
localVue.use(BootstrapVue);

describe('DetailsComponent.vue', () => {

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
            details:null
        },
        number_of_dates: 1,
    };

	it('Input state change', () => {
		chai.assert(false, "TODO - DONT THINK WE TEST THIS?");

	});

	it('Date format casting', () => {
		const detailsDateWrapper = mount(Details, {
			propsData: {
				callback: () => {},
				data: index_data,
				errors:{
					messages:[],
					fields:[]
				}
			},
			localVue
		});
		chai.assert( detailsDateWrapper.vm.castToMoment(index_data.index_market_object.expiry_dates[0]) == "Sep19", "The passed date (2019-09-19 00:00:00) was casted to format MMMYY (Sep19)");

	});

	it('Submit Details', () => {
		chai.assert(false, "TODO");

	});

	it('Set input states', () => {
		const detailsInputStateWrapper = mount(Details, {
			propsData: {
				callback: () => {},
				data: index_data,
				errors:{
					messages:["Error message"],
					fields:["trade_structure_groups.0.fields.Strike"]
				}
			},
			localVue
		});

		chai.assert( detailsInputStateWrapper.vm.inputState(1, "Strike") == null ,"Return null - No error present for that index");
		chai.assert.isFalse( detailsInputStateWrapper.vm.inputState(0, "Strike") ,"Return false - Error present for that item on that index");
		chai.assert( detailsInputStateWrapper.vm.inputState(0,"Quantity") == null ,"Return null - No error present for that item");
	});

	describe('Mounted Data', () => {

		it('Outright', () => {
			chai.assert(false, "TODO");

		});

		it('Risky', () => {
			chai.assert(false, "TODO");

		});

		it('Fly', () => {
			chai.assert(false, "TODO");

		});

		it('Calendar', () => {
			chai.assert(false, "TODO");

		});
	});
});