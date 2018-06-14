/*import { shallowMount } from '@vue/test-utils';
import ActionBar from '../../resources/assets/js/components/ActionBarComponent.vue';
import { EventBus } from '../../resources/assets/js/lib/EventBus.js';

const Market = require('../../resources/assets/js/lib/Market');

describe('ActionBarComponent.vue', () => {

	it('Chat bar gets loaded when event gets emitted', (done) => {		
		const toggleTestWrapper = shallowMount(ActionBar, { 
			methods: {
				toggleBar: () => {done()}
			}
		});
		EventBus.$emit('toggleSidebar', 'chat');
	});

	it('Chat bar emits event when closed', (done) => {
		const loadChatBarWrapper = shallowMount(ActionBar, {
			methods: {
				loadChatBar: () => {done()}
			}
		});
		loadChatBarWrapper.find('#chat-bar-dismiss').trigger('click');
	});
});*/
