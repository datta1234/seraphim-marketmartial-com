import Vue from 'vue';
export const EventBus = new Vue();


EventBus.$on('toggleSidebar', (sidebar, state, payload) => {
	switch(sidebar) {    
        case "interaction":
        	EventBus.$emit('chatToggle', false);
        	EventBus.$emit('interactionToggle', state, payload);
        break;
        case "chat":
        	EventBus.$emit('interactionToggle', false);
            EventBus.$emit('chatToggle', state, payload);
        break;
    }
});