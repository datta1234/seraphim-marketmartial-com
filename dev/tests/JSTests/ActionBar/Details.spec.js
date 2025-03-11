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
            expiry_dates:["2019-09-19 00:00:00"],
            details:null
        },
        number_of_dates: 1,
    };

    beforeEach(function() {
		index_data.index_market_object = {
            market:test_markets[0],
            trade_structure: 'Outright',
            trade_structure_groups: [],
            expiry_dates:["2019-09-19 00:00:00"],
            details:null
        };
	});

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
		chai.assert.equal( detailsDateWrapper.vm.castToMoment(index_data.index_market_object.expiry_dates[0]), "Sep19", "The passed date (2019-09-19 00:00:00) was casted to format MMMYY (Sep19)");

	});

	it('Submit Details', (done) => {
		let testFields = {
			fields:[
				{is_selected:true,strike:58899645,quantity:4589},
		        {is_selected:false,strike:60899645,quantity:4589},
		        {is_selected:true,strike:68899645,quantity:4589}
			]
		};
		let submitDetailsCallback = (form_data) => {
			chai.assert.deepEqual(index_data.index_market_object.details , form_data, "The saved form data is equal to passed data details");
			done();
		};

		index_data.index_market_object.trade_structure = 'Fly';

		const submitDetailsWrapper = mount(Details, {
			propsData: {
				callback: submitDetailsCallback,
				data: index_data,
				errors:{
					messages:[],
					fields:[]
				}
			},
			localVue
		});

		testFields.fields.forEach( (element, index) => {
			submitDetailsWrapper.vm.$data.form_data.fields[index].strike = element.strike;
			submitDetailsWrapper.vm.$data.form_data.fields[index].quantity = element.quantity;
		});


		submitDetailsWrapper.vm.$nextTick( () => {
			chai.assert.deepEqual(submitDetailsWrapper.vm.$data.form_data, testFields, "The set form data is equal to test data");
			submitDetailsWrapper.find('#index-details-form').trigger('submit');
		});
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

		chai.assert.isNull( detailsInputStateWrapper.vm.inputState(1, "Strike"),"Return null - No error present for that index");
		chai.assert.isFalse( detailsInputStateWrapper.vm.inputState(0, "Strike") ,"Return false - Error present for that item on that index");
		chai.assert.isNull( detailsInputStateWrapper.vm.inputState(0,"Quantity"),"Return null - No error present for that item");
	});

	describe('Mounted Data Fields', () => {

		it('Outright', () => {
			index_data.index_market_object.trade_structure = 'Outright';

			const detailsOutrightWrapper = mount(Details, {
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

			chai.assert.isFalse(detailsOutrightWrapper.vm.$data.display.show_expiry,'Hide expiry dates');
			chai.assert.lengthOf(detailsOutrightWrapper.vm.$data.form_data.fields,1,'One form data field added');
			chai.assert(detailsOutrightWrapper.vm.$data.form_data.fields[0].is_selected,'The first form data field is selected');
			chai.assert(detailsOutrightWrapper.vm.$data.display.disable_choice,'Choice disabled');
			chai.assert.isNull(detailsOutrightWrapper.vm.$data.chosen_option,'No default chosen option');
		});

		it('Risky', () => {
			index_data.index_market_object.trade_structure = 'Risky';

			const detailsRiskyWrapper = mount(Details, {
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

			chai.assert.isFalse(detailsRiskyWrapper.vm.$data.display.show_expiry,'Hide expiry dates');
			chai.assert.lengthOf(detailsRiskyWrapper.vm.$data.form_data.fields,2,'Two form data field added');
			chai.assert(detailsRiskyWrapper.vm.$data.form_data.fields[0].is_selected,'The first form data field is selected');
			chai.assert.isFalse(detailsRiskyWrapper.vm.$data.display.disable_choice,'Choice enabled');
			chai.assert.equal(detailsRiskyWrapper.vm.$data.chosen_option, 0,'Default chosen option index is 0');
		});

		it('Fly', () => {
			index_data.index_market_object.trade_structure = 'Fly';

			const detailsFlyWrapper = mount(Details, {
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

			chai.assert.isFalse(detailsFlyWrapper.vm.$data.display.show_expiry,'Hide expiry dates');
			chai.assert.lengthOf(detailsFlyWrapper.vm.$data.form_data.fields,3,'Three form data field added');
			chai.assert(detailsFlyWrapper.vm.$data.form_data.fields[0].is_selected,'The first form data field is selected');
			chai.assert(detailsFlyWrapper.vm.$data.form_data.fields[2].is_selected,'The last form data field is selected');
			chai.assert(detailsFlyWrapper.vm.$data.display.disable_choice,'Choice disabled');
			chai.assert.isNull(detailsFlyWrapper.vm.$data.chosen_option,'No default chosen option');
		});

		it('Calendar', () => {
			index_data.index_market_object.trade_structure = 'Calendar';

			const detailsCalendarWrapper = mount(Details, {
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

			chai.assert(detailsCalendarWrapper.vm.$data.display.show_expiry,'Show expiry dates');
			chai.assert.lengthOf(detailsCalendarWrapper.vm.$data.form_data.fields,2,'Two form data field added');
			chai.assert(detailsCalendarWrapper.vm.$data.form_data.fields[0].is_selected,'The first form data field is selected');
			chai.assert.isFalse(detailsCalendarWrapper.vm.$data.display.disable_choice,'Choice enabled');
			chai.assert.equal(detailsCalendarWrapper.vm.$data.chosen_option, 0,'Default chosen option index is 0');
		});
	});
});