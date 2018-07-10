import { mount } from '@vue/test-utils';
import ImportantMenu from '../../../resources/assets/js/components/ActionBar/Components/ImportantMenuComponent.vue';
import Market from '../../../resources/assets/js/lib/Market.js';
import UserMarketRequest from '../../../resources/assets/js/lib/UserMarketRequest.js';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

// create an extended `Vue` constructor
const localVue = createLocalVue();
// install boostrap to this vue instance only
localVue.use(BootstrapVue);

describe('ImportantMenuComponent.vue', () => {

	let test_data = {
        display_markets: [],
        no_cares: [],
        important_count_per_market: 1
	};

	beforeEach(function() {
    	test_data.display_markets = [
        	new Market({
        		id: 1,
		        title: "Test title",
		        description: "Test discription",
		        order: 1,
		        market_type_id: 2,
		        market_requests: [
					new UserMarketRequest({id: "1"}),
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
					new UserMarketRequest({id: "5", attributes:{state:"alert"}}),
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
				],
			}),
        ];
    	test_data.no_cares = [];
  	});

	describe('Viewing Important Markets', () => {

		it('Important Markets applies no cares to a Single Market', () => {
			const loadImportantNocareWrapper = mount(ImportantMenu, {
				propsData: {
		        	markets: test_data.display_markets,
		        	no_cares: test_data.no_cares,
			    },
				localVue
			});

			chai.assert.deepPropertyVal(loadImportantNocareWrapper.vm.notificationList, test_data.display_markets[0].title, [test_data.display_markets[0].market_requests[0]], 'test_data.display_markets[0].market_requests[2] market request is present in the computed Confirmations Notification list');
			loadImportantNocareWrapper.find('#action-important-button').trigger('click');
			loadImportantNocareWrapper.find('#important-nocare-' + test_data.display_markets[0].market_requests[0].id).trigger('click');
			chai.assert.notDeepPropertyVal(loadImportantNocareWrapper.vm.notificationList, test_data.display_markets[0].title, [test_data.display_markets[0].market_requests[0]], 'test_data.display_markets[0].market_requests[2] market request is NOT present in the computed Confirmations Notification list');
		});

		it('Important Markets applies bulk no cares to Markets', () => {
			const loadImportantBulkNocareWrapper = mount(ImportantMenu, {
				propsData: {
		        	markets: test_data.display_markets,
		        	no_cares: test_data.no_cares,
			    },
				localVue
			});

			chai.assert.deepPropertyVal(loadImportantBulkNocareWrapper.vm.notificationList, test_data.display_markets[0].title, [test_data.display_markets[0].market_requests[0]], 'test_data.display_markets[0].market_requests[0] market request is present in the computed Confirmations Notification list');
			chai.assert.deepPropertyVal(loadImportantBulkNocareWrapper.vm.notificationList, test_data.display_markets[1].title, [test_data.display_markets[1].market_requests[0]], 'test_data.display_markets[1].market_requests[0] market request is present in the computed Confirmations Notification list');
			chai.assert.deepPropertyVal(loadImportantBulkNocareWrapper.vm.notificationList, test_data.display_markets[2].title, [test_data.display_markets[2].market_requests[1]], 'test_data.display_markets[2].market_requests[1] market request is present in the computed Confirmations Notification list');
			chai.assert.isEmpty(test_data.no_cares);
			
			let no_care_ids = [test_data.display_markets[0].market_requests[0].id, test_data.display_markets[1].market_requests[0].id, test_data.display_markets[2].market_requests[1].id];
			
			loadImportantBulkNocareWrapper.find('#action-important-button').trigger('click');
			loadImportantBulkNocareWrapper.find('#select-bulk-nocares').trigger('click');
			loadImportantBulkNocareWrapper.find('#apply-bulk-nocares-button').trigger('click');

			chai.assert.notDeepPropertyVal(loadImportantBulkNocareWrapper.vm.notificationList, test_data.display_markets[0].title, [test_data.display_markets[0].market_requests[0]], 'test_data.display_markets[0].market_requests[0] market request is NOT present in the computed Confirmations Notification list');
			chai.assert.notDeepPropertyVal(loadImportantBulkNocareWrapper.vm.notificationList, test_data.display_markets[1].title, [test_data.display_markets[1].market_requests[0]], 'test_data.display_markets[1].market_requests[0] market request is NOT present in the computed Confirmations Notification list');
			chai.assert.notDeepPropertyVal(loadImportantBulkNocareWrapper.vm.notificationList, test_data.display_markets[2].title, [test_data.display_markets[2].market_requests[1]], 'test_data.display_markets[2].market_requests[1] market request is NOT present in the computed Confirmations Notification list');
			chai.assert.deepEqual(test_data.no_cares, no_care_ids, "The nocares list should contain all the removed market_request ids");
		});

		it('Important Markets Menu emits close when dismissing a popover', (done) => {
			const closePopoverWrapper = mount(ImportantMenu, {
				propsData: {
		        	markets: test_data.display_markets,
		        	no_cares: test_data.no_cares,
			    },
				localVue
			});

			closePopoverWrapper.vm.$refs[closePopoverWrapper.vm.$data.popover_ref].$on("close", () => {
				done();
			});

			closePopoverWrapper.find('#action-important-button').trigger('click');
			closePopoverWrapper.find('#dismiss-important-popover').trigger('click');
		});

		it('Compiles a list of Important Markets from the passed market prop', () => {
			const notificationListWrapper = mount(ImportantMenu, {
				propsData: {
		        	markets: test_data.display_markets,
		        	no_cares: test_data.no_cares,
			    },
				localVue
			});

			chai.assert.deepPropertyVal(notificationListWrapper.vm.notificationList, test_data.display_markets[0].title, [test_data.display_markets[0].market_requests[0]], 'test_data.display_markets[0].market_requests[0] market request is present in the computed Confirmations Notification list');
			chai.assert.lengthOf(notificationListWrapper.vm.notificationList[test_data.display_markets[0].title], test_data.important_count_per_market, 'array has length of 1');

			chai.assert.deepPropertyVal(notificationListWrapper.vm.notificationList, test_data.display_markets[1].title, [test_data.display_markets[1].market_requests[0]], 'test_data.display_markets[1].market_requests[0] market request is present in the computed Confirmations Notification list');
			chai.assert.lengthOf(notificationListWrapper.vm.notificationList[test_data.display_markets[1].title], test_data.important_count_per_market, 'array has length of 1');

			chai.assert.deepPropertyVal(notificationListWrapper.vm.notificationList, test_data.display_markets[2].title, [test_data.display_markets[2].market_requests[1]], 'test_data.display_markets[2].market_requests[1] market request is present in the computed Confirmations Notification list');
			chai.assert.lengthOf(notificationListWrapper.vm.notificationList[test_data.display_markets[2].title], test_data.important_count_per_market, 'array has length of 1');			
		});
	});
});