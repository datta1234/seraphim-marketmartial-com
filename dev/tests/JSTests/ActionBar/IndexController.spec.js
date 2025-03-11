import { shallowMount } from '@vue/test-utils';
import IndexController from '../../../resources/assets/js/components/ActionBar/Components/RequestMarket/Controllers/IndexControllerComponent.vue';
import Market from '../../../resources/assets/js/lib/Market.js';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

// create an extended `Vue` constructor
const localVue = createLocalVue();
//Mock Vue $root data for component
localVue.use((Vue) => {
	Vue.prototype.market_types = {
		market_types:[
			{"id": 1,"title": "Index Option"},
			{"id": 2,"title": "Delta One(EFPs, Rolls and EFP Switches)"},
			{"id": 3,"title": "Single Stock Options"}
		]
	};
});
// install boostrap to this vue instance only
localVue.use(BootstrapVue);

describe('IndexControllerComponent.vue', () => {

	let req_modal_data = {};

	beforeEach(function() {
		req_modal_data = {
			title:'Select A Market',
	        step: 2,
	        show_modal: true,
	        modal_ref: 'request-market-ref',
	        selected_controller: null,
		};
	});

	it('Shallow Mount not working so well - components are an issue', () => {
		chai.assert(false, "TODO - FIX THIS SOME HOW, all tests are written and working if components can be stubbed properly");
	});

	/*it('Load Index Markets', () => {
		const indexControllerLoadMarketsWrapper = shallowMount(IndexController, {
			propsData: {
				callback: () => {},
				modal_data: req_modal_data,
				close_modal: () => {}
			},
			localVue
		});
		chai.assert.deepEqual(indexControllerLoadMarketsWrapper.vm.$data.index_data.market_type , {"id": 3,"title": "Single Stock Options"}, "$root vue Index market type should be loaded");
	});

	it('Date format casting', () => {
		const indexControllerDateCastingWrapper = shallowMount(IndexController, {
			propsData: {
				callback: () => {},
				modal_data: req_modal_data,
				close_modal: () => {}
			},
			localVue
		});
		chai.assert.equal( indexControllerDateCastingWrapper.vm.castToMoment("2019-09-19 00:00:00"), "2019-09-19", "The passed date (2019-09-19 00:00:00) was casted to format YYYY-MM-DD (2019-09-19)");
	});

	it('Mount load initial step' , (done) => {
		const indexControllerMountWrapper = shallowMount(IndexController, {
			propsData: {
				callback: () => {},
				modal_data: req_modal_data,
				close_modal: () => {}
			},
			methods: {
					loadStepComponent: () => {
						done();
					},
				},
			localVue
		});
		chai.assert.equal(indexControllerMountWrapper.vm.$data.modal_data.title, "Index", "Modal title changed to Index");
		chai.assert.isNotNull(indexControllerMountWrapper.vm.$data.index_data.market_type, "Market type loaded");
		chai.assert.equal(indexControllerMountWrapper.vm.$data.selected_step_component, "Market", "Initial component set to Market");
		indexControllerMountWrapper.vm.$emit('modal_step');
	});

	describe('Change contoller step', () => {
		const indexControllerStepWrapper = shallowMount(IndexController, {
			propsData: {
				callback: () => {},
				modal_data: req_modal_data,
				close_modal: () => {}
			},
			localVue
		});

		beforeEach(function() {
			indexControllerStepWrapper.vm.$props.modal_data.step = 2;
		});

		it('Next Step', () => {
			chai.assert.equal( indexControllerStepWrapper.vm.modal_data.step, 2, "Initial step is set to 2");
			indexControllerStepWrapper.vm.nextStep();
			chai.assert.equal( indexControllerStepWrapper.vm.modal_data.step, 3, "Step increased to 3");

		});

		it('Previous Step', () => {
			indexControllerStepWrapper.vm.$props.modal_data.step = 5;
			indexControllerStepWrapper.vm.previousStep();
			chai.assert.equal( indexControllerStepWrapper.vm.modal_data.step, 4, "Step decrease to 4");			
		});

		it('Set Lowest Step', () => {
			indexControllerStepWrapper.vm.$props.modal_data.step = 3;
			indexControllerStepWrapper.vm.setLowestStep(4);
			chai.assert.equal( indexControllerStepWrapper.vm.modal_data.step, 3, "Step should have remained the same");	
			indexControllerStepWrapper.vm.setLowestStep(2);
			chai.assert.equal( indexControllerStepWrapper.vm.modal_data.step, 2, "Step changed to 2");	
		});
	});

	describe('Load Step Component', () => {

		const indexControllerStepComponentWrapper = shallowMount(IndexController, {
			propsData: {
				callback: () => {},
				modal_data: req_modal_data,
				close_modal: () => {}
			},
			methods: {
				saveMarketRequest: () => {
					done();
				},
			},
			localVue
		});

		it('Select a Market', () => {
			indexControllerStepWrapper.vm.$props.modal_data.step = 1;
			indexControllerStepWrapper.vm.loadStepComponent();
			chai.assert.equal(indexControllerStepWrapper.vm.$data.selected_step_component, 'Market', "Market is the loaded component");
		});

		it('Select a Structure', () => {
			indexControllerStepWrapper.vm.$props.modal_data.step = 2;
			indexControllerStepWrapper.vm.loadStepComponent();
			chai.assert.equal(indexControllerStepWrapper.vm.$data.selected_step_component, 'Structure', "Structure is the loaded component");
		});

		it('Select Single Expiry Date', () => {
			indexControllerStepComponentWrapper.vm.$data.index_data.index_market_object.trade_structure = 'Outright';
			indexControllerStepWrapper.vm.$props.modal_data.step = 3;
			indexControllerStepWrapper.vm.loadStepComponent();
			chai.assert.equal(indexControllerStepWrapper.vm.$data.selected_step_component, 'Expiry', "Expiry is the loaded component");
		});

		it('Select Two Expiry Date', () => {
			indexControllerStepComponentWrapper.vm.$data.index_data.index_market_object.trade_structure = 'Calendar';
			indexControllerStepWrapper.vm.$props.modal_data.step = 3;
			indexControllerStepWrapper.vm.loadStepComponent();
			chai.assert.equal(indexControllerStepWrapper.vm.$data.selected_step_component, 'Expiry', "Expiry is the loaded component");
			chai.assert.equal(indexControllerStepWrapper.vm.$data.index_data.number_of_dates, 2, "Number of dates are 2");
		});

		it('Fill out Details', () => {
			indexControllerStepWrapper.vm.$props.modal_data.step = 4;
			indexControllerStepWrapper.vm.loadStepComponent();
			chai.assert.equal(indexControllerStepWrapper.vm.$data.selected_step_component, 'Details', "Details is the loaded component");
		});

		it('Confirm Market Request', () => {
			indexControllerStepWrapper.vm.$props.modal_data.step = 4;
			indexControllerStepWrapper.vm.loadStepComponent();
			chai.assert.equal(indexControllerStepWrapper.vm.$data.selected_step_component, 'Confirm', "Confirm is the loaded component");
			chai.assert.equal(indexControllerStepWrapper.vm.$props.modal_data.title, 'Confirm Market Request', "Confirm is the loaded component");
		});

		it('Save Market', (done) => {
			indexControllerStepWrapper.vm.$props.modal_data.step = 6;
			indexControllerStepWrapper.vm.loadStepComponent();
		});

		it('Step Back', () => {
			indexControllerStepWrapper.vm.$props.modal_data.step = 4;
			indexControllerStepWrapper.vm.loadStepComponent('back');
			chai.assert.equal(indexControllerStepWrapper.vm.$data.selected_step_component, 'Details', "Details is the loaded component");
			chai.assert.equal(indexControllerStepWrapper.vm.$props.modal_data.step, 4, "Steps remain the same");
		});
	});

	describe('Save new Market Request', () => {

		let test_markets = [
			new Market({id:1,title:"TOP40",market_type_id:1,is_displayed:1}),
			new Market({id:2,title:"CTOP",market_type_id:1,is_displayed:1}),
			new Market({id:3,title:"CTOR",market_type_id:1,is_displayed:1})
		];
		let test_data = {
	        market_type_title:'Index Option',
	        market_type: {
	        	id: 1,
				title: "Index Option",
				markets: test_markets
			},
	        index_market_object: {
	            market:test_markets[0],
	            trade_structure: 'Risky',
	            trade_structure_groups: [],
	            expiry_dates:["2019-09-19 00:00:00"],
	            details: {
			      "fields": [
			        {
			          "is_selected": true,
			          "strike": "456456",
			          "quantity": 500
			        },
			        {
			          "is_selected": false,
			          "strike": "456456",
			          "quantity": 500
			        }
			      ]
			    }
	        },
	        number_of_dates: 1,
	    };

		const indexControllerSaveWrapper = shallowMount(IndexController, {
			propsData: {
				callback: () => {},
				modal_data: req_modal_data,
				close_modal: () => {}
			},
			data: {
				index_data: test_data
			}
			localVue
		});

		it('Format Data', () => {
			let formatted_data = {
				"trade_structure": "Risky",
				"trade_structure_groups": [
					{
						"is_selected": true,
						"market_id": 1,
						"fields": {
							"Expiration Date": "2019-09-19",
							"Strike": "456456",
							"Quantity": 500
						}
					},
					{
						"is_selected": false,
						"market_id": 1,
						"fields": {
							"Expiration Date": "2019-09-19",
							"Strike": "456456",
							"Quantity": 500
						}
					}
				]
			};
			chai.assert.deepEqual(formatted_data, indexControllerSaveWrapper.vm.formatRequestData(), 'Index data formatted to correctly to be sent to API');
		});

		it('Post new Market Request', () => {
			chai.assert(false, "TODO - DO WE TEST THIS?");
			//mock fake api and stub the axios call, get moxios to work perhaps or use sinon.
		});
	});

	describe('Load error step', () => {
		
		const indexControllerErrorsWrapper = shallowMount(IndexController, {
			propsData: {
				callback: () => {},
				modal_data: req_modal_data,
				close_modal: () => {}
			},
			localVue
		});

		beforeEach(function() {
			indexControllerErrorsWrapper.vm.$data.errors.data.Market.messages = [];
			indexControllerErrorsWrapper.vm.$data.errors.data.Structure.messages = [];
			indexControllerErrorsWrapper.vm.$data.errors.data.Expiry.messages = [];
			indexControllerErrorsWrapper.vm.$data.errors.data.Details.messages = [];
			indexControllerErrorsWrapper.vm.$data.errors.data.Details.fields = [];
			indexControllerErrorsWrapper.vm.$data.errors.data.Confirm.messages = [];

			indexControllerErrorsWrapper.vm.$props.modal_data.step = 6;
		});

		it('Market Error', () => {
			let market_errors = {
				"trade_structure_groups.0.market_id": [
					"Please set a market.",
					"Please select a valid market."
				]
			};

			chai.assert.isEmpty(indexControllerErrorsWrapper.vm.$data.errors.data.Market.messages, "Market error messages array is empty");
			indexControllerErrorsWrapper.vm.loadErrorStep(market_errors);
			chai.assert.deepEqual(indexControllerErrorsWrapper.vm.$data.errors.data.Market.messages, market_errors["trade_structure_groups.0.market_id"] , "The Market error messages are equal to the passed error messages");
			chai.assert.equal( indexControllerErrorsWrapper.vm.modal_data.step, 1, "Lowest step set to 1");
		});

		it('Choice Details Error', () => {
			let choice_errors = {
				"trade_structure_groups.0.is_selected": [
					"Please select a valid choice"
				],
				"trade_structure_groups.1.is_selected": [
			      	"The trade structure groups.1.is selected field is required."
			    ]
			};
			
			chai.assert.isEmpty(indexControllerErrorsWrapper.vm.$data.errors.data.Details.messages, "Details error messages array is empty");
			indexControllerErrorsWrapper.vm.loadErrorStep(choice_errors);
			chai.assert.include(indexControllerErrorsWrapper.vm.$data.errors.data.Details.messages, choice_errors["trade_structure_groups.0.is_selected"][0] , "The Details error messages are equal to the passed Choice 0 error messages");
			chai.assert.include(indexControllerErrorsWrapper.vm.$data.errors.data.Details.messages, choice_errors["trade_structure_groups.1.is_selected"][0] , "The Details error messages are equal to the passed Choice 1 error messages");
			chai.assert.equal( indexControllerErrorsWrapper.vm.modal_data.step, 4, "Lowest step set to 4");
		});

		it('Expiry Date Error', () => {
			let expiry_date_errors = {
				"trade_structure_groups.0.fields.Expiration Date": [
					"Expiration Date is required.",
					"Please ensure that the selected Expiration Date is a valid date.",
	      			"Please select a date that is after today."
				]
			};

			chai.assert.isEmpty(indexControllerErrorsWrapper.vm.$data.errors.data.Expiry.messages, "Expiry error messages array is empty");
			indexControllerErrorsWrapper.vm.loadErrorStep(expiry_date_errors);
			chai.assert.deepEqual(indexControllerErrorsWrapper.vm.$data.errors.data.Expiry.messages, expiry_date_errors["trade_structure_groups.0.fields.Expiration Date"] , "The Expiry error messages are equal to the passed error messages");
			chai.assert.equal( indexControllerErrorsWrapper.vm.modal_data.step, 3, "Lowest step set to 3");
		});

		it('Details Error', () => {
			let details_errors = {
				"trade_structure_groups.0.fields.Strike": [
					"Strike is required.",
					"Please ensure that you enter a valid Strike amount."
				],
				"trade_structure_groups.0.fields.Quantity": [
					"Quantity is required.",
					"Please ensure that you enter a valid Quantity amount."
				],
			};
			
			chai.assert.isEmpty(indexControllerErrorsWrapper.vm.$data.errors.data.Details.messages, "Details error messages array is empty");
			indexControllerErrorsWrapper.vm.loadErrorStep(details_errors);

			chai.assert.include(indexControllerErrorsWrapper.vm.$data.errors.data.Details.messages, details_errors["trade_structure_groups.0.fields.Strike"][0] , "The Expiry error messages are equal to the passed Strike[0] error messages");
			chai.assert.include(indexControllerErrorsWrapper.vm.$data.errors.data.Details.messages, details_errors["trade_structure_groups.0.fields.Strike"][1] , "The Expiry error messages are equal to the passed Strike[1] error messages");

			chai.assert.include(indexControllerErrorsWrapper.vm.$data.errors.data.Details.messages, details_errors["trade_structure_groups.0.fields.Quantity"][0] , "The Expiry error messages are equal to the passed Quantity[0] error messages");
			chai.assert.include(indexControllerErrorsWrapper.vm.$data.errors.data.Details.messages, details_errors["trade_structure_groups.0.fields.Quantity"][1] , "The Expiry error messages are equal to the passed Quantity[1] error messages");
		
			chai.assert.deepEqual(indexControllerErrorsWrapper.vm.$data.errors.data.Details.fields, ["trade_structure_groups.0.fields.Strike","trade_structure_groups.0.fields.Quantity"] , "The Expiry error fields are equal to the passed error message fields");
			chai.assert.equal( indexControllerErrorsWrapper.vm.modal_data.step, 4, "Lowest step set to 4");
		});

		it('General Details Error', () => {
			let gen_details_errors = {
				"trade_structure_groups": [
					"Generic trade structure group error"
				]
			};
			
			chai.assert.isEmpty(indexControllerErrorsWrapper.vm.$data.errors.data.Details.messages, "Details error messages array is empty");
			indexControllerErrorsWrapper.vm.loadErrorStep(gen_details_errors);
			chai.assert.deepEqual(indexControllerErrorsWrapper.vm.$data.errors.data.Details.messages, gen_details_errors["trade_structure_groups"] , "The Details error messages are equal to the passed error messages");
			chai.assert.equal( indexControllerErrorsWrapper.vm.modal_data.step, 4, "Lowest step set to 4");
		});

		it('Generic Error', () => {
			let generic_errors = {
				"trade_structure_groups.0.stock": [
					"Please select a valid stock."
				],
			};
			
			chai.assert.isEmpty(indexControllerErrorsWrapper.vm.$data.errors.data.Confirm.messages, "Confirm error messages array is empty");
			indexControllerErrorsWrapper.vm.loadErrorStep(generic_errors);
			chai.assert.deepEqual(indexControllerErrorsWrapper.vm.$data.errors.data.Confirm.messages, generic_errors["trade_structure_groups.0.stock"] , "The Confirm error messages are equal to the passed error messages");
			chai.assert.equal( indexControllerErrorsWrapper.vm.modal_data.step, 6, "Step did not change to 6");
		});

		it('Structure Error', () => {
			let structure_errors = {
				"trade_structure": [
					"The trade structure field is required."
				]
			};
			
			chai.assert.isEmpty(indexControllerErrorsWrapper.vm.$data.errors.data.Structure.messages, "Structure error messages array is empty");
			indexControllerErrorsWrapper.vm.loadErrorStep(structure_errors);
			chai.assert.deepEqual(indexControllerErrorsWrapper.vm.$data.errors.data.Structure.messages, structure_errors["trade_structure"] , "The Structure error messages are equal to the passed error messages");
			chai.assert.equal( indexControllerErrorsWrapper.vm.modal_data.step, 2, "Lowest step set to 2");
		});
	});*/
});