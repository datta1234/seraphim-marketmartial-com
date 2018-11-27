import axios from 'axios';

const state = {
    recieved: [],
    completed: [],
    expected: [],
    max_length: 100
}

const recieve = (hash) => {
    // console.log("[Stream] RECEIVED ", hash);
    if(state.recieved.indexOf(hash) == -1) {
        state.recieved.push(hash);
    }
    clearMatches();
}
const expect = (hash) => {
    // console.log("[Stream] EXPECTING ", hash);
    if(state.expected.indexOf(hash) == -1) {
        state.expected.push(hash);
    }
    clearMatches();
}
const complete = (hash) => {
    // console.log("[Stream] COMPLETED ", hash);
    // maintain history of max_length
    if(state.completed.length >= state.max_length) {
        state.completed.shift();
    }
    state.completed.push(hash);
}

const clearMatches = () => {
    state.recieved.forEach(val => {
        // found an expected that we recieved
        if(state.expected.indexOf(val) !== -1) {
            state.expected.splice(state.expected.indexOf(val), 1);
            let val = state.recieved.splice(state.recieved.indexOf(val), 1)[0];
            complete(val);
            emitter.emit(val);
        }
    });
}

const emitter = {
    clients: [],
    emit: (key) => {
        emitter.clients.forEach(fn => fn(key));
    },
    attach: (cb) => {
        if(emitter.clients.indexOf(cb) == -1) {
            // console.log("[Stream] ATTACHED ", cb);
            emitter.clients.push(cb);
            return true;
        }
        return true;
    },
    detach: (cb) => {
        if(emitter.clients.indexOf(cb) != -1) {
            // console.log("[Stream] DETATCHED ", cb);
            emitter.clients.splice(emitter.clients.indexOf(cb), 1);
            return true;
        }
        return false
    }
}

export default {
    recieve: recieve,
    expect: expect,
    interface: emitter
}   