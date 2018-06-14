import { mount } from '@vue/test-utils';
import ChatBar from '../../resources/assets/js/components/ChatBarComponent.vue';
import { EventBus } from '../../resources/assets/js/lib/EventBus.js';

describe('ChatBarComponent.vue', () => {

	it('Chat bar gets loaded when event gets emitted', (done) => {		
		const toggleTestWrapper = mount(ChatBar, { 
			methods: {
				toggleBar: () => {done()}
			}
		});
		EventBus.$emit('toggleSidebar', 'chat');
	});

	it('Chat bar emits event when closed', (done) => {
		const loadChatBarWrapper = mount(ChatBar, {
			methods: {
				loadChatBar: () => {done()}
			}
		});
		loadChatBarWrapper.find('#chat-bar-dismiss').trigger('click');
	});
});
