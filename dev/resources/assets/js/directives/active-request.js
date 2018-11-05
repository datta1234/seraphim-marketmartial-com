import axios from 'axios';
import Stream from '~/services/Stream';

let awaiting_stream = false;
const ActiveRequestState = {
    _ignore_header: "ignore",
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

const init = (app) => {
    ActiveRequestState._ignore_header = app.$root.config('app.ajax.headers.ignore');
    console.log("INitialised ActiveRequest", app, ActiveRequestState);

    Stream.interface.attach((key) => {
        setTimeout(() => {
            console.log("Completion Of Wait Time");
            awaiting_stream = false;
            ActiveRequestState.toggleElements();
        }, 0);
    });

    // Add a request interceptor
    axios.interceptors.request.use((config) => {

        // handle load
        if(typeof config.headers[ActiveRequestState._ignore_header] === 'undefined') {
            ActiveRequestState.active_requests++;
            ActiveRequestState.toggleElements();
        }

        return config;
    }, (error) => {
        if(ActiveRequestState.active_requests > 0) {
            ActiveRequestState.active_requests--;
        }
        ActiveRequestState.toggleElements();

        return Promise.reject(error);
    });

    // Add a response interceptor
    axios.interceptors.response.use((response) => {
         // headers catchment
        if(response.headers && typeof response.headers['pending-streams'] != 'undefined') {
            response.headers['pending-streams'].split(',').forEach(key => {
                Stream.expect(key);
                console.log("Triggered Header: ", key);
            });
            awaiting_stream = true;
        }

        // handle unload
        if(typeof response.config.headers[ActiveRequestState._ignore_header] === 'undefined') {
            if(ActiveRequestState.active_requests > 0) {
                ActiveRequestState.active_requests--;
            }
            ActiveRequestState.toggleElements();
        }
        return response;
    }, (error, data) => {
        if(ActiveRequestState.active_requests > 0) {
            ActiveRequestState.active_requests--;
        }
        ActiveRequestState.toggleElements();

        return Promise.reject(error);
    });
}

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
    },
    init: init
};
    