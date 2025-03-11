import { mount } from '@vue/test-utils';
import ExpirySelection from '../../../resources/assets/js/components/ActionBar/Components/RequestMarket/Components/ExpirySelectionComponent.vue';
import Market from '../../../resources/assets/js/lib/Market.js';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

// create an extended `Vue` constructor
const localVue = createLocalVue();
// install boostrap to this vue instance only
localVue.use(BootstrapVue);

describe('ExpirySelectionComponent.vue', () => {

	let test_markets = [
		new Market({id:1,title:"TOP40",market_type_id:1,is_displayed:1}),
		new Market({id:2,title:"CTOP",market_type_id:1,is_displayed:1}),
		new Market({id:3,title:"CTOR",market_type_id:1,is_displayed:1})
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
            expiry_dates:[],
            details:null
        },
        number_of_dates: 1,
    };

    beforeEach(function() {
		index_data.index_market_object.expiry_dates = [];
	});

	it('Load Expiry Date', () => {
		chai.assert(false, "TODO - DO WE TEST THIS?");
		//mock fake api and stub the axios call, get moxios to work perhaps or use sinon.

	});

	it('Select single Expiry Date', (done) => {
		let test_date = "2019-09-19 00:00:00";
		
		let selectExpiryCallback = () => {
			chai.assert.lengthOf(index_data.index_market_object.expiry_dates,1,"Only one date should be selected");
			chai.assert.equal(index_data.index_market_object.expiry_dates[0],test_date,"The selected date should be added to passed data expiry_dates array");
			done();
		};
		const selectSingleExpiryWrapper = mount(ExpirySelection, {
			propsData: {
				callback: selectExpiryCallback,
				data: index_data,
				errors:{
					messages:[],
					fields:[]
				}
			},
			localVue
		});

		selectSingleExpiryWrapper.vm.selectExpiryDates(test_date);
	});

	it('Select multiple Expiry Dates', (done) => {
		let test_dates = ["2019-09-19 00:00:00","2019-11-19 00:00:00"];

		let selectExpiryCallback = () => {
			chai.assert.lengthOf(index_data.index_market_object.expiry_dates,2,"Two dates should be selected");
			chai.assert.equal(index_data.index_market_object.expiry_dates[0],test_dates[0],"The 1ste selected date should be added to passed data expiry_dates array");
			chai.assert.equal(index_data.index_market_object.expiry_dates[1],test_dates[1],"The 2nd selected date should be added to passed data expiry_dates array");
			done();
		};

		index_data.number_of_dates = 2;

		const selectMultipleExpiryWrapper = mount(ExpirySelection, {
			propsData: {
				callback: selectExpiryCallback,
				data: index_data,
				errors:{
					messages:[],
					fields:[]
				}
			},
			localVue
		});

		selectMultipleExpiryWrapper.vm.selectExpiryDates(test_dates[0]);
		selectMultipleExpiryWrapper.vm.selectExpiryDates(test_dates[1]);
	});

	it('Date format casting', () => {
		const expiryDateCastingWrapper = mount(ExpirySelection, {
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
		chai.assert.equal( expiryDateCastingWrapper.vm.castToMoment("2019-09-19 00:00:00"), "Sep19", "The passed date (2019-09-19 00:00:00) was casted to format MMMYY (Sep19)");

	});

	it('Past Date check', () => {
		const expiryDatePastWrapper = mount(ExpirySelection, {
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
		chai.assert( expiryDatePastWrapper.vm.checkPastDate( moment().subtract(1, 'years').format("YYYY-MM-DD HH:mm:ss") ), "The passed date (2016-09-19 00:00:00) is a past date");
		chai.assert.isFalse( expiryDatePastWrapper.vm.checkPastDate( moment().add(1, 'years').format("YYYY-MM-DD HH:mm:ss") ), "The passed date (3000-09-19 00:00:00) is NOT a past date");
	});

	it('Pagination page change', () => {
		chai.assert(false, "TODO - DO WE TEST THIS?");
		//idea 1 - overload loadExpiryDate().
		//idea 2 - mock fake api and stub the axios, get moxios to work.

	});
});