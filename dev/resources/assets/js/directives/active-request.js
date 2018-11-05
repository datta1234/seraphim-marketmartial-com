import axios from 'axios';
import Stream from '~/services/Stream';

let awaiting_stream = false;
const ActiveRequestState = {
    // _instance: null,
    _ignore_header: "ignore",
    active_requests: 0,
    registered_elements: [],
    registered_context: [],
    toggleElements: () => {
        ActiveRequestState.registered_elements.forEach(x => {
            ActiveRequestState.toggleElement(x);
        });
    },
    toggleElement: (el) => {
        if (ActiveRequestState.active_requests > 0) {
            console.log("[ActiveRequest] TOGGLE ON");
            el.setAttribute('mm-disabled', true);
        } else {
            if(!awaiting_stream) {
                console.log("[ActiveRequest] TOGGLE OFF");
                el.removeAttribute('mm-disabled');
            }
        }
    }
};

const init = (app) => {
    ActiveRequestState._ignore_header = app.$root.config('app.ajax.headers.ignore');

    Stream.interface.attach((key) => {
        setTimeout(() => {
            console.log("[ActiveRequest] RECEIVED STREAM");
            awaiting_stream = false;
            ActiveRequestState.toggleElements();
        }, 0);
    });

    // Add a request interceptor
    axios.interceptors.request.use((config) => {

        // handle load
        if(typeof config.headers[ActiveRequestState._ignore_header] === 'undefined') {
            ActiveRequestState.active_requests++;
            console.log("[ActiveRequest] SENDING REQUEST");
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
        // handle unload
        if(typeof response.config.headers[ActiveRequestState._ignore_header] === 'undefined') {
            // headers catchment
            if(response.headers && typeof response.headers['pending-streams'] != 'undefined') {
                response.headers['pending-streams'].split(',').forEach(key => {
                    Stream.expect(key);
                });
                awaiting_stream = true;
            }

            if(ActiveRequestState.active_requests > 0) {
                ActiveRequestState.active_requests--;
            }
            console.log("[ActiveRequest] RECEIVED RESPONSE");
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
            ActiveRequestState.toggleElement(el);
        }
    },
    update: (el, binding, vnode, oldVnode) => {
        // ActiveRequestState.toggleElements(); // dont update every time
    },
    unbind: (el, binding, vnode, oldVnode) => {
        if(ActiveRequestState.registered_elements.indexOf(el) !== -1) {
            ActiveRequestState.registered_elements.splice(ActiveRequestState.registered_elements.indexOf(el), 1);
        }  
    },
    init: init
};
    