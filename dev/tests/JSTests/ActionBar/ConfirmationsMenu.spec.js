import { mount } from '@vue/test-utils';
import ConfirmationsMenu from '../../../resources/assets/js/components/ActionBar/Components/ConfirmationsMenuComponent.vue';
import Market from '../../../resources/assets/js/lib/Market.js';
import UserMarketRequest from '../../../resources/assets/js/lib/UserMarketRequest.js';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

// create an extended `Vue` constructor
const localVue = createLocalVue();
// install boostrap to this vue instance only
localVue.use(BootstrapVue);

describe('ConfirmationsMenuComponent.vue', () => {


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
					new UserMarketRequest({id: "3", attributes:{state:"confirm"}})
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
					new UserMarketRequest({id: "5", attributes:{state:"confirm"}}),
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
					new UserMarketRequest({id: "7", attributes:{state:"confirm"}}),
					new UserMarketRequest({id: "8"}),
					new UserMarketRequest({id: "9"})
				],
			}),
        ],
        confirmations_count_per_market: 1
	};

	describe('Viewing Confirmations', () => {

		it('Confirmation modal loads with correct data', () => {
			const loadConfirmationsModalWrapper = mount(ConfirmationsMenu, {
				propsData: {
		        	markets: test_data.display_markets,
			    },
				localVue
			});

			chai.assert(!loadConfirmationsModalWrapper.vm.$data.modals.show_modal, "modal open state should false");
			chai.assert.notDeepEqual( loadConfirmationsModalWrapper.vm.$data.modals.market_request, test_data.display_markets[0].market_requests[2], "The Modal request market data should NOT be the selected market request");
			loadConfirmationsModalWrapper.find('#action-confirmations-button').trigger('click');
			loadConfirmationsModalWrapper.find('#confirmations-view-' + test_data.display_markets[0].market_requests[2].id).trigger('click');
			chai.assert(loadConfirmationsModalWrapper.vm.$data.modals.show_modal, "modal open state should true");
			chai.assert.deepEqual( loadConfirmationsModalWrapper.vm.$data.modals.market_request, test_data.display_markets[0].market_requests[2], "The Modal request market data should be the selected market request");
		});

		it('Confirmations Menu emits close when dismissing a popover', (done) => {
			const closePopoverWrapper = mount(ConfirmationsMenu, {
				propsData: {
		        	markets: test_data.display_markets,
			    },
				localVue
			});

			closePopoverWrapper.vm.$refs[closePopoverWrapper.vm.$data.popover_ref].$on("close", () => {
				done();
			});

			closePopoverWrapper.find('#action-confirmations-button').trigger('click');
			closePopoverWrapper.find('#dismiss-confirmations-popover').trigger('click');
		});

		it('Compiles a list of Confirmations from the passed market prop', () => {
			const notificationListWrapper = mount(ConfirmationsMenu, {
				propsData: {
		        	markets: test_data.display_markets,
			    },
				localVue
			});

			chai.assert.deepPropertyVal(notificationListWrapper.vm.notificationList, test_data.display_markets[0].title, [test_data.display_markets[0].market_requests[2]], 'test_data.display_markets[0].market_requests[2] market request is present in the computed Confirmations Notification list');
			chai.assert.lengthOf(notificationListWrapper.vm.notificationList[test_data.display_markets[0].title], test_data.confirmations_count_per_market, 'array has length of 1');
			
			chai.assert.deepPropertyVal(notificationListWrapper.vm.notificationList, test_data.display_markets[1].title, [test_data.display_markets[1].market_requests[1]], 'test_data.display_markets[1].market_requests[1] market request is present in the computed Confirmations Notification list');
			chai.assert.lengthOf(notificationListWrapper.vm.notificationList[test_data.display_markets[1].title], test_data.confirmations_count_per_market, 'array has length of 1');

			chai.assert.deepPropertyVal(notificationListWrapper.vm.notificationList, test_data.display_markets[2].title, [test_data.display_markets[2].market_requests[0]], 'test_data.display_markets[2].market_requests[0] market request is present in the computed Confirmations Notification list');
			chai.assert.lengthOf(notificationListWrapper.vm.notificationList[test_data.display_markets[2].title], test_data.confirmations_count_per_market, 'array has length of 1');
		});
	});
});