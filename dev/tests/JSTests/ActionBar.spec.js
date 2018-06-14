import { shallowMount } from '@vue/test-utils';
import ActionBar from '../../resources/assets/js/components/ActionBarComponent.vue';

import FilterMarketsMenu from '../../resources/assets/js//components/ActionBar/Components/FilterMarketsMenuComponent.vue';
import ImportantMenu from '../../resources/assets/js//components/ActionBar/Components/ImportantMenuComponent.vue';
import AlertsMenu from '../../resources/assets/js//components/ActionBar/Components/AlertsMenuComponent.vue';
import ConfirmationsMenu from '../../resources/assets/js//components/ActionBar/Components/ConfirmationsMenuComponent.vue';

import { EventBus } from '../../resources/assets/js/lib/EventBus.js';

import Market from '../../resources/assets/js/lib/Market';
import UserMarketRequest from '../../resources/assets/js/lib/UserMarketRequest';

describe('ActionBarComponent.vue', () => {

	let test_data = {
		no_cares: [],
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
					new UserMarketRequest({id: "3"})
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
					new UserMarketRequest({id: "5"}),
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
					new UserMarketRequest({id: "7"}),
					new UserMarketRequest({id: "8"}),
					new UserMarketRequest({id: "9"})
				],
			}),
        ]
	};
	describe('Market Quantity counters', ()=> {
		
		it('Market Quantity counters have the correct values', () => {
			chai.assert(false, "TODO");

		});

		it('Market Quantity counters update correctly', () => {
			chai.assert(false, "TODO");

		});
	});

	describe('Chat functions', ()=> {
		
		it('Action bar emits event to open chatbar', (done) => {		
			chai.assert(false, "TODO");

			const toggleTestWrapper = shallowMount(ActionBar, {
				/*propsData: {
			        markets: test_data.display_markets,
			        no_cares: test_data.no_cares
			      },*/
				methods: {
					toggleBar: () => {done()}
				}
			});
			//EventBus.$emit('toggleSidebar', 'chat');
		});

		it('Action bar listens for a chatbar close event', () => {		
			chai.assert(false, "TODO");

			/*const toggleTestWrapper = shallowMount(ActionBar, { 
				methods: {
					toggleBar: () => {done()}
				}
			});
			EventBus.$emit('toggleSidebar', 'chat');*/
		});
	});

	describe('Request a Market', () => {

		it('Opens Request a Market modal', () => {
			chai.assert(false, "TODO");

		});

		it('Closes Request a Market modal', () => {
			chai.assert(false, "TODO");

		});
	});
});
