import { shallowMount } from '@vue/test-utils';
import IndexController from '../../../resources/assets/js/components/ActionBar/Components/RequestMarket/Controllers/IndexControllerComponent.vue';
import Market from '../../../resources/assets/js/lib/Market.js';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

// create an extended `Vue` constructor
const localVue = createLocalVue();
// install boostrap to this vue instance only
localVue.use(BootstrapVue);

describe('IndexControllerComponent.vue', () => {

	it('Load Index Markets', () => {
		chai.assert(false, "TODO");

	});

	it('Date format casting', () => {
		chai.assert(false, "TODO");
		/*const indexControllerDateCastingWrapper = shallowMount(IndexController, {
			propsData: {
				callback: () => {},
				modal_data: {
					title:'Select A Market',
                    step: 0,
                    show_modal: false,
                    modal_ref: 'request-market-ref',
                    selected_controller: null,
				},
				close_modal: () => {}
			},
			localVue
		});
		chai.assert.equal( indexControllerDateCastingWrapper.vm.castToMoment("2019-09-19 00:00:00"), "2019-09-19", "The passed date (2019-09-19 00:00:00) was casted to format YYYY-MM-DD (2019-09-19)");*/
	});

	it('Mount load initial step' , () => {
		chai.assert(false, "TODO");
	});

	describe('Change contoller step', () => {

		it('Next Step', () => {
			chai.assert(false, "TODO");

		});

		it('Previous Step', () => {
			chai.assert(false, "TODO");

		});

		it('Load Step Component', () => {
			chai.assert(false, "TODO");

		});

		it('Set Lowest Step', () => {
			chai.assert(false, "TODO");

		});
	});

	describe('Load Step Component', () => {

		it('Select a Market', () => {
			chai.assert(false, "TODO");

		});

		it('Select a Structure', () => {
			chai.assert(false, "TODO");

		});

		it('Select Expiry Date', () => {
			chai.assert(false, "TODO");

		});

		it('Fill out Details', () => {
			chai.assert(false, "TODO");

		});

		it('Confirm Market Request', () => {
			chai.assert(false, "TODO");

		});

		it('Save Market', () => {
			chai.assert(false, "TODO");

		});
	});

	describe('Save new Market Request', () => {

		it('Format Data', () => {
			chai.assert(false, "TODO");

		});

		it('Post new Market Request', () => {
			chai.assert(false, "TODO - DO WE TEST THIS?");
			//mock fake api and stub the axios call, get moxios to work perhaps or use sinon.
		});
	});

	describe('Load error step', () => {

		it('Market Error', () => {
			chai.assert(false, "TODO");

		});

		it('Choice Details Error', () => {
			chai.assert(false, "TODO");

		});

		it('Expiry Date Error', () => {
			chai.assert(false, "TODO");

		});

		it('General Details Error', () => {
			chai.assert(false, "TODO");

		});

		it('Generic Error', () => {
			chai.assert(false, "TODO");

		});

		it('Generic Choice Error', () => {
			chai.assert(false, "TODO");

		});

		it('Structure Error', () => {
			chai.assert(false, "TODO");

		});
	});
});