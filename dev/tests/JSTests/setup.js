// setup JSDOM
require('jsdom-global')();

// make assertion library chai available globally
global.chai = require('chai');

// make sinon for spies and stubs available globally
global.sinon = require('sinon');

// make moment library available globally
global.moment = require('moment');

// make moment library available globally
global.axios = require('axios');
global.axios.defaults.baseUrl = "";