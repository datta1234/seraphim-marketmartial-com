import axios from 'axios';

const INTERVAL = 1000*60; // once a minute
const state = {
    total: 0,
    keepalive: null
};

axios.interceptors.response.use((response) => {
    if(response.headers && typeof response.headers['active-market-makers'] != 'undefined') {
        this.total = response.headers['active-market-makers'];
    }
    return response;
});
state.keepalive.setInterval(() => {
    axios.get(axios.defaults.baseUrl + '/ping')
    .then(res => {
        if(res.data != 'pong') {
            //console.error("Ping Event: ", event);
            let re = confirm("Live update stream may have disconnected!\n\nThis may be due to session inactivity.\n\nRefresh Page Now?");
            if(re) {
                location.reload();
            }
        } 
    })
    .catch(err => {
        //console.error("Ping Event: ", event);
        let re = confirm("Live update stream may have disconnected!\n\nThis may be due to session inactivity.\n\nRefresh Page Now?");
        if(re) {
            location.reload();
        }
    });
}, INTERVAL);
