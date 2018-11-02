import axios from 'axios';
import Stream from '~/services/Stream';

let awaiting_stream = false;
const ActiveRequestState = {
    active_requests: 0,
    registered_elements: [],
    toggleElements: () => {
        if (ActiveRequestState.active_requests > 0) {
            // console.log("Toggling Off ["+ActiveRequestState.registered_elements.length+"] Elements");
            ActiveRequestState.registered_elements.forEach(x => x.setAttribute('disabled', '') );
        } else {
            // console.log("Toggling On ["+ActiveRequestState.registered_elements.length+"] Elements");
            if(!awaiting_stream) {
                ActiveRequestState.registered_elements.forEach(x => x.removeAttribute('disabled') );
            }
        }
    }
};

Stream.interface.attach((key) => {
    setTimeout(() => {
        console.log("Completion Of Wait Time");
        awaiting_stream = false;
        ActiveRequestState.toggleElements();
    }, 0);
});

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

    // headers catchment
    if(response.headers && typeof response.headers['pending-streams'] != 'undefined') {
        response.headers['pending-streams'].split(',').forEach(key => {
            Stream.expect(key);
            console.log("Triggered Header: ", key);
        });
        awaiting_stream = true;
    }
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
    