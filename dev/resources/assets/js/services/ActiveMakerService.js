import axios from 'axios';

const INTERVAL = 1000*60; // once a minute
const state = {
    _ignore_header: "ignore",
    clients: [],
    total: 0,
    keepalive: null
};

const ping = () => {
    axios.get(axios.defaults.baseUrl + '/ping', {
        headers: {
            [state._ignore_header]: true
        }
    })
    .then(res => {
        if(res.data != 'pong') {
            console.error("Ping Event: ", event);
            let re = confirm("Live update stream may have disconnected!\n\nThis may be due to session inactivity.\n\nRefresh Page Now?");
            if(re) {
                location.reload();
            }
        } 
    })
    .catch(err => {
        console.error("Ping Event: ", event);
        let re = confirm("Live update stream may have disconnected!\n\nThis may be due to session inactivity.\n\nRefresh Page Now?");
        if(re) {
            location.reload();
        }
    });
}

const init = (app) => {
    state._ignore_header = app.$root.config('app.ajax.headers.ignore');
    console.log("INitialised ActiveMaker", app, state);
    // ensure it happens only once
    if(state.keepalive == null) {
        axios.interceptors.response.use((response) => {
            if(response.headers && typeof response.headers['active-market-makers'] != 'undefined') {
                state.total = response.headers['active-market-makers'];
                emitter.emit(state.total);
            }
            return response;
        });
        state.keepalive = setInterval(ping, INTERVAL);
    }
}

const emitter = {
    clients: [],
    emit: (key) => {
        emitter.clients.forEach(fn => fn(key));
    },
    attach: (cb) => {
        if(emitter.clients.indexOf(cb) == -1) {
            emitter.clients.push(cb);
            ping();
            return true;
        }
        return true;
    },
    detach: (cb) => {
        if(emitter.clients.indexOf(cb) != -1) {
            emitter.clients.splice(emitter.clients.indexOf(cb), 1);
            ping();
            return true;
        }
        return false
    }
}

export default {
    interface: emitter,
    init: init
}   