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

// make Vue library available globally
global.Vue = require('vue');

// set a global base url default for axios
global.axios.defaults.baseUrl = "http://unit.marketmartial.test";

// make nock library available globally
global.nock = require('nock');