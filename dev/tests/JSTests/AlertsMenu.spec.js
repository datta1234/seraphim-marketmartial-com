import { mount } from '@vue/test-utils';
import AlertsMenu from '../../resources/assets/js/components/ActionBar/Components/AlertsMenuComponent.vue';
import Market from '../../resources/assets/js/lib/Market.js';
import UserMarketRequest from '../../resources/assets/js/lib/UserMarketRequest.js';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';
import { EventBus } from '../../resources/assets/js/lib/EventBus.js';

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
			let assertCompleteAlert = (sidebar, state, payload) => {
				chai.assert(sidebar == "interaction", "sidebar should be equal to interaction");
				chai.assert(state, "The emitted state should be true");
				chai.assert.deepEqual( payload, test_data.display_markets[0].market_requests[2], "The payload should be the selected market request");
				EventBus.$off('toggleSidebar', assertCompleteAlert);
				done();
			};

			const loadInteractionBarWrapper = mount(AlertsMenu, {
				propsData: {
		        	markets: test_data.display_markets,
			    },
				localVue
			});

			EventBus.$on('toggleSidebar', assertCompleteAlert);
			loadInteractionBarWrapper.find('#action-alert-button').trigger('click');
			loadInteractionBarWrapper.find('#alert-view-' + test_data.display_markets[0].market_requests[2].id).trigger('click');
		});

		it('Alerts Menu emits close when dismissing a popover', (done) => {
			const closePopoverWrapper = mount(AlertsMenu, {
				propsData: {
		        	markets: test_data.display_markets,
			    },
				localVue
			});
			closePopoverWrapper.vm.$refs[closePopoverWrapper.vm.$data.popover_ref].$on("close", () => {
				done();
			});
			closePopoverWrapper.find('#action-alert-button').trigger('click');
			closePopoverWrapper.find('#dismiss-alert-popover').trigger('click');
		});

		it('Compiles a list of Alerts from the passed market prop', () => {
			const notificationListWrapper = mount(AlertsMenu, {
				propsData: {
		        	markets: test_data.display_markets,
			    },
				localVue
			});

			chai.assert.deepPropertyVal(notificationListWrapper.vm.notificationList, test_data.display_markets[0].title, [test_data.display_markets[0].market_requests[2]], 'test_data.display_markets[0].market_requests[2] market request is present in the computed Alert Notification list');
			chai.assert.deepPropertyVal(notificationListWrapper.vm.notificationList, test_data.display_markets[1].title, [test_data.display_markets[1].market_requests[1]], 'test_data.display_markets[1].market_requests[1] market request is present in the computed Alert Notification list');
			chai.assert.deepPropertyVal(notificationListWrapper.vm.notificationList, test_data.display_markets[2].title, [test_data.display_markets[2].market_requests[0]], 'test_data.display_markets[2].market_requests[0] market request is present in the computed Alert Notification list');
		});
	});
});