import { mount } from '@vue/test-utils';
import AlertsMenu from '../../resources/assets/js/components/ActionBar/Components/AlertsMenuComponent.vue';
import Market from '../../resources/assets/js/lib/Market.js';
import UserMarketRequest from '../../resources/assets/js/lib/UserMarketRequest.js';
import { createLocalVue } from '@vue/test-utils'
import BootstrapVue from 'bootstrap-vue'

// create an extended `Vue` constructor
const localVue = createLocalVue();
// install boostrap to this vue instance only
localVue.use(BootstrapVue);

describe('AlertsMenuComponent.vue', () => {

	let test_data = {
        display_markets: [
        	new Market({
        		id: 1,
		        title: "Test title",
		        description: "Test discription",
		        order: 1,
		        market_type_id: 2,
		        market_requests: [
					new UserMarketRequest({id: "1"}),
					new UserMarketRequest({id: "2"}),
					new UserMarketRequest({id: "3", attributes:{state:"vol-spread-alert"}})
				],
			}),
        	new Market({
        		id: 2,
		        title: "Test title 2",
		        description: "Test discription 2",
		        order: 3,
		        market_type_id: 2,
		        market_requests: [
					new UserMarketRequest({id: "4"}),
					new UserMarketRequest({id: "5", attributes:{state:"vol-spread-alert"}}),
					new UserMarketRequest({id: "6"})
				],
			}),
        	new Market({
        		id: 3,
		        title: "Test title 3",
		        description: "Test discription 3",
		        order: 2,
		        market_type_id: 2,
		        market_requests: [
					new UserMarketRequest({id: "7", attributes:{state:"alert"}}),
					new UserMarketRequest({id: "8"}),
					new UserMarketRequest({id: "9"})
				],
			}),
        ]
	};
	describe('Viewing Alerts', () => {

		it('Alerts Menu emits event when viewing an alert', (done) => {
			const loadInteractionBarWrapper = mount(AlertsMenu, {
				propsData: {
		        	markets: test_data.display_markets,
			    },
				methods: {
					loadInteractionBar: () => {done()}
				},
				localVue
			});
			loadInteractionBarWrapper.find('#action-alert-button').trigger('click');
			loadInteractionBarWrapper.find('#alert-view-' + test_data.display_markets[0].market_requests[2].id).trigger('click');
		});

		it('Alerts Menu emits close when dismissing a popover', () => {
			chai.assert(false, "TODO");
		});

		it('Compiles a list of Alerts from the passed market prop', () => {
			const loadInteractionBarWrapper = mount(AlertsMenu, {
				propsData: {
		        	markets: test_data.display_markets,
			    },
				localVue
			});
			//let alertlist = loadInteractionBarWrapper.computed.notificationList;
			console.log("HERE: ",loadInteractionBarWrapper.vm);
		});
	});
});