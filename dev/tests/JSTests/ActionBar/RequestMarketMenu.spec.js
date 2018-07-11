import { shallowMount } from '@vue/test-utils';
import RequestMarketMenu from '../../../resources/assets/js/components/ActionBar/Components/RequestMarket/RequestMarketMenuComponent.vue';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

// create an extended `Vue` constructor
const localVue = createLocalVue();
localVue.use((Vue) => {
	Vue.prototype.market_types = {
		market_types:["TOP40", "DTOP", "DCAP"]
	};
});
// install boostrap to this vue instance only
localVue.use(BootstrapVue);

describe('RequestMarketMenuComponent.vue', () => {

	it('Shallow Mount not working so well - components are an issue', () => {
		chai.assert(false, "TODO - FIX THIS SOME HOW, all tests are written and working if components can be stubbed properly");
	});

	/*describe('Request a Market Modal', () => {

		it('Show Modal', (done) => {
			const requestMarketMenuShowWrapper = shallowMount(RequestMarketMenu, {
				methods: {
					hideModal: () => {
						done();
					},
				},
				localVue
			});
			requestMarketMenuShowWrapper.vm.showModal();
			chai.assert.equal( requestMarketMenuShowWrapper.vm.modal_data.selected_controller, "Selections", "Selections is the initial selected contoller component");
			chai.assert.equal( requestMarketMenuShowWrapper.vm.modal_data.step, 0, "Current step is set to 0");
			chai.assert( requestMarketMenuShowWrapper.vm.modal_data.show_modal, "Modal set to show");
			requestMarketMenuShowWrapper.vm.$refs[requestMarketMenuShowWrapper.vm.$data.modal_data.modal_ref].$emit('hidden');
		});

		it('Close Modal', () => {
			const requestMarketMenuCloseWrapper = shallowMount(RequestMarketMenu, { localVue });
			requestMarketMenuCloseWrapper.vm.hideModal();
			chai.assert.isNull( requestMarketMenuCloseWrapper.vm.modal_data.selected_controller, "Selected contoller component should be null");
			chai.assert.equal( requestMarketMenuCloseWrapper.vm.modal_data.step, 0, "Current step is reset to 0");
			chai.assert.isFalse( requestMarketMenuCloseWrapper.vm.modal_data.show_modal, "Modal set to show");
			chai.assert.equal( requestMarketMenuCloseWrapper.vm.modal_data.title, "Select A Market", "Title should be reset to Select A Market");
		});
	});

	describe('Modal steps', () => {

		it('Next Step', () => {
			const requestMarketNextWrapper = shallowMount(RequestMarketMenu, { localVue });
			chai.assert.equal( requestMarketNextWrapper.vm.modal_data.step, 0, "Initial step is set to 0");
			requestMarketNextWrapper.vm.nextStep();
			chai.assert.equal( requestMarketNextWrapper.vm.modal_data.step, 1, "Step increased to 1");
		});

		it('Previous Step current conrtoller', (done) => {
			let modal_step_callback = () => {
				done();
			};
			const requestMarketPrevWrapper = shallowMount(RequestMarketMenu, { localVue });
			
			requestMarketPrevWrapper.vm.modal_data.step = 5;
			requestMarketPrevWrapper.vm.$refs['currentController'].$on('modal_step', modal_step_callback);
			
			requestMarketPrevWrapper.vm.previousStep();
			chai.assert.equal( requestMarketPrevWrapper.vm.modal_data.step, 4, "Step decreased to 4");
		});

		it('Previous Step select new conrtoller', () => {
			const requestMarketPrev2Wrapper = shallowMount(RequestMarketMenu, { localVue });
			requestMarketPrev2Wrapper.vm.modal_data.step = 2;
			requestMarketPrev2Wrapper.vm.previousStep();
			chai.assert.equal( requestMarketPrev2Wrapper.vm.modal_data.step, 0, "Step reset to 0");
			chai.assert.equal( requestMarketPrev2Wrapper.vm.modal_data.selected_controller, "Selections", "Reset to Selections contoller component");
		});
	});

	describe('Controller', () => {

		it('Load selected contoller', () => {
			const requestMarketControllerWrapper = shallowMount(RequestMarketMenu, { localVue });
			chai.assert.equal( requestMarketNextWrapper.vm.modal_data.step, 0, "Initial step is set to 0");
			chai.assert.isNull( requestMarketMenuCloseWrapper.vm.modal_data.selected_controller, "Selected contoller component should be null");
			requestMarketControllerWrapper.vm.loadController("Index");
			chai.assert.equal( requestMarketNextWrapper.vm.modal_data.step, 1, "Process started step set to 1");
			chai.assert.equal( requestMarketPrev2Wrapper.vm.modal_data.selected_controller, "Index", "Contoller component changed to Index");
		});
	});*/
});