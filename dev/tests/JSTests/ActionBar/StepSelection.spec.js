import { mount } from '@vue/test-utils';
import StepSelection from '../../../resources/assets/js/components/ActionBar/Components/RequestMarket/Components/StepSelectionComponent.vue';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

// create an extended `Vue` constructor
const localVue = createLocalVue();
// install boostrap to this vue instance only
localVue.use(BootstrapVue);

describe('StepSelectionComponent.vue', () => {

	it('Index Step selection', (done) => {
		let stepSelectionCallback = (step) => {
			chai.assert(step == "Index", "Index Options is the selected step");
			done();
		};

		const indexSelectionWrapper = mount(StepSelection, {
			propsData: {
				callback: stepSelectionCallback,
				modal_data: {step: 0}
			},
			localVue
		});

		indexSelectionWrapper.find('#index-step-choice').trigger('click');
	});
});