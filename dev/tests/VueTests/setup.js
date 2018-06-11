// setup JSDOM
require('jsdom-global')()

// make assertion library chai available globally
global.chai = require('chai');

// make sinon for spies and stubs available globally
global.sinon = require('sinon');