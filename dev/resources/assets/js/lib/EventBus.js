import Vue from 'vue';
export const EventBus = new Vue();

/**
 * Listens to a global toggleSidebar event
 *
 * @event EventBus#toggleSidebar
 * 
 * @type {string} $sidebar An argument detailing which sidebar is being toggled.
 * @type {boolean} $state An argument detailing what state the sidebar being
 *		toggled is in.
 * @type {*} $payload An argument detailing the payload being transmitted with the
 *		event.
 *  
 * @fires EventBus#chatToggle
 * @fires EventBus#interactionToggle
 */
EventBus.$on('toggleSidebar', (sidebar, state, payload) => {
	switch(sidebar) {
        case "interaction":
        	//EventBus.$emit('chatToggle', false);
        	EventBus.$emit('interactionToggle', state, payload);
        break;
        case "chat":
        	//EventBus.$emit('interactionToggle', false);
            EventBus.$emit('chatToggle', state, payload);
        break;
    }
});

// @TODO Add loaders to elements that need them
EventBus.$on('loading', (type, state) => {
    switch(type) {
        case "page":
            EventBus.$emit("pageLoaded", state);
        break;
        case "requestStructure":
            EventBus.$emit("requestStructureLoaded", state);
        break;
        case "requestDates":
            EventBus.$emit("requestDatesLoaded", state);
        break;
        case "requestSubmission":
            EventBus.$emit("requestSubmissionLoaded", state);
       case "confirmationSubmission":
            EventBus.$emit("confirmationSubmissionLoaded", state);
        break;
        case "requestMarkets":
            EventBus.$emit("requestMarketsLoaded", state);
        break;
    }
});

EventBus.$on('theme', (state) => {
    EventBus.$emit("toggleTheme", state);
});

EventBus.$on('force-display-update', (val) => {
    EventBus.$emit('display-update-forced', val);
});

EventBus.$on('startReset', (val) => {
    EventBus.$emit('resetStarted', val);
});
EventBus.$on('completeReset', (val) => {
    EventBus.$emit('resetComplete', val);
});
EventBus.$on('removeMarketRequest', (val) => {
   EventBus.$emit('marketRequestRemoved', val); 
});

EventBus.$on('disableNegotiationInput', () => {
   EventBus.$emit('negotiationInputDisabled'); 
});
EventBus.$on('enableNegotiationInput', () => {
   EventBus.$emit('negotiationInputEnabled'); 
});

EventBus.$on('resetConditions', () => {
    EventBus.$emit('conditionsReset');
})