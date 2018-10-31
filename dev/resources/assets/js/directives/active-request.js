import axios from 'axios';

const ActiveRequestState = {
    active_requests: 0,
    registered_elements: [],
    toggleElements: () => {
        if (ActiveRequestState.active_requests > 0) {
            // console.log("Toggling Off ["+ActiveRequestState.registered_elements.length+"] Elements");
            ActiveRequestState.registered_elements.forEach(x => x.setAttribute('disabled', '') );
        } else {
            // console.log("Toggling On ["+ActiveRequestState.registered_elements.length+"] Elements");
            ActiveRequestState.registered_elements.forEach(x => x.removeAttribute('disabled') );
        }
    }
};

// Add a request interceptor
axios.interceptors.request.use((config) => {
    ActiveRequestState.active_requests++;
    ActiveRequestState.toggleElements();

    return config;
}, (error) => {
    ActiveRequestState.active_requests--;
    ActiveRequestState.toggleElements();

    return Promise.reject(error);
});

// Add a response interceptor
axios.interceptors.response.use((response) => {
    ActiveRequestState.active_requests--;
    ActiveRequestState.toggleElements();

    return response;
}, (error) => {
    ActiveRequestState.active_requests--;
    ActiveRequestState.toggleElements();

    return Promise.reject(error);
});

export default {
    bind: (el, binding, vnode, oldVnode) => {
        if(ActiveRequestState.registered_elements.indexOf(el) == -1) {
            ActiveRequestState.registered_elements.push(el);
            ActiveRequestState.toggleElements();
        }
    },
    update: (el, binding, vnode, oldVnode) => {
        ActiveRequestState.toggleElements();
    },
    unbind: (el, binding, vnode, oldVnode) => {
        if(ActiveRequestState.registered_elements.indexOf(el) !== -1) {
            ActiveRequestState.registered_elements.splice(ActiveRequestState.registered_elements.indexOf(el), 1);
        }  
    }
};
    