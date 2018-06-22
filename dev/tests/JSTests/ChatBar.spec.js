import { mount } from '@vue/test-utils';
import ChatBar from '../../resources/assets/js/components/ChatBarComponent.vue';
import { EventBus } from '../../resources/assets/js/lib/EventBus.js';

describe('ChatBarComponent.vue', () => {

	it('Chat bar gets loaded when event gets emitted', () => {		
		const toggleTestWrapper = mount(ChatBar, {});
		chai.assert(!toggleTestWrapper.vm.$data.opened ,'The Chat bar opened state should be false');
		EventBus.$emit('toggleSidebar', 'chat');
		chai.assert(toggleTestWrapper.vm.$data.opened ,'The Chat bar opened state should be true');
	});

	it('Chat bar emits event when closed', (done) => {
		let assertCompleteChat = (sidebar) => {
			chai.assert(sidebar == "chat", "sidebar should be equal to chat");
			EventBus.$off('toggleSidebar', assertCompleteChat);
			done();
		};

		const loadChatBarWrapper = mount(ChatBar, {});

		EventBus.$on('toggleSidebar', assertCompleteChat);
		loadChatBarWrapper.find('#chat-bar-dismiss').trigger('click');
	});
});
