import { mount, shallowMount } from '@vue/test-utils';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

import Market from '~/lib/Market.js';
import UserMarketRequest from '~/lib/UserMarketRequest.js';
import UserMarket from '~/lib/UserMarket.js';
import UserMarketNegotiation from '~/lib/UserMarketNegotiation.js';
import ApplyConditionsComponent from '~/components/InteractionBar/MarketComponents/ApplyConditionsComponent.vue';
import { EventBus } from '~/lib/EventBus.js';

// create an extended `Vue` constructor
const localVue = createLocalVue();
// install boostrap to this vue instance only
localVue.use(BootstrapVue);
localVue.use((Vue) => {
    Vue.prototype.config = function(path) {
        const data = {
            "trade_structure.outright.expiration_date": "Expiration Date",
            "trade_structure.outright.strike": "Strike",
        };
        return data[path];
    };
})

describe.only('ApplyConditionsComponent.vue', () => {

    let test_data;

    before(function() {
        test_data = {
            market: new Market(require('../mockData/Market.json'))
        };
    });

    describe("getCategoryOptions", function(){

        let component;

        beforeEach(function() {
            component = shallowMount(OutrightLayout, {
                propsData: {
                    marketRequest: test_data.market.market_requests[0]
                },
                localVue
            });
            component.vm.conditions = require('../mockData/Conditions.json')['conditions'];
            component.vm.categories = require('../mockData/Conditions.json')['categories'];
        });

       

    });

    describe("mutateCategories", function(){

    });

    describe("loadConditions", function(){

    });

    describe("resetCategorySelection", function(){

    });

    describe("applyCondition", function(){

    });

    describe("applyCategory", function(){

    });

    describe("recurseSelected", function(){

    });

    describe("updateCategoryConditions", function(){

    });


});