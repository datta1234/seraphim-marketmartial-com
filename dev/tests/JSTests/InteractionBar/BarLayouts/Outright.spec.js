import { mount, shallowMount } from '@vue/test-utils';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

import Market from '~/lib/Market.js';
import UserMarketRequest from '~/lib/UserMarketRequest.js';
import UserMarket from '~/lib/UserMarket.js';
import UserMarketNegotiation from '~/lib/UserMarketNegotiation.js';
import OutrightLayout from '~/components/InteractionBar/BarLayouts/Outright.vue';
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

describe('OutrightLayout.vue', () => {

    let test_data;

    before(function() {
        test_data = {
            market: new Market(require('../mockData/Market.json'))
        };
    });

    it("should call `init` on mounted", function(done) {
        shallowMount(OutrightLayout, {
            propsData: {
                marketRequest: test_data.market.market_requests[0]
            },
            methods: {
                init: () => {
                    done();
                }
            },
            localVue
        });
    })


    

    describe('#init()', () => {
        
        let component;

        beforeEach(function() {
            component = shallowMount(OutrightLayout, {
                propsData: {
                    marketRequest: test_data.market.market_requests[0]
                },
                localVue
            });
        });

        it('should call `#reset()` method', () => {
            let reset = sinon.stub(component.vm, 'reset');
            component.vm.init();
            chai.assert(reset.calledOnce);
        });

        it('should create new proposed UserMarket', () => {
            component.vm.init();
            chai.assert(component.vm.proposed_user_market instanceof UserMarket)
        });

        it('should create new proposed UserMarketNegotiation', () => {
            component.vm.init();
            chai.assert(component.vm.proposed_user_market_negotiation instanceof UserMarketNegotiation)
        });

        it('should associate UserMarketNegotiation to UserMarket', () => {
            component.vm.init();
            chai.assert.strictEqual(component.vm.proposed_user_market_negotiation.getUserMarket(), component.vm.proposed_user_market)
            chai.assert.strictEqual(component.vm.proposed_user_market.getCurrentNegotiation(), component.vm.proposed_user_market_negotiation)
        });

        // it('should call create new proposed UserMarketNegotiation', () => {
            
        //     let reset = sinon.stub(component.vm, 'reset');

        //     component.vm.init();

        //     chai.assert(reset.calledOnce);

        // });

    });

    describe('#sendQuote()', () => {
        
        let component;

        beforeEach(function() {
            component = shallowMount(OutrightLayout, {
                propsData: {
                    marketRequest: test_data.market.market_requests[0]
                },
                localVue
            });
            nock(axios.defaults.baseUrl)
            .defaultReplyHeaders({ 'access-control-allow-origin': '*' })
            .post('/trade/market-request/'+component.vm.marketRequest.id+'/user-market')
            .reply(200, function() {
                return 'success';
            })
            .persist();//always handles
        });

        

        it('should associate UserMarket as UserMarketRequest', () => {
            
            chai.assert.notStrictEqual(component.vm.proposed_user_market.getMarketRequest(), component.vm.marketRequest)
            component.vm.sendQuote();
            chai.assert.strictEqual(component.vm.proposed_user_market.getMarketRequest(), component.vm.marketRequest)

        });

        it('should call `UserMarket.store()` method', () => {
            let storeCall = sinon.stub(component.vm.proposed_user_market, 'store');
            storeCall.resolves('success');
            component.vm.sendQuote();
            chai.assert(storeCall.calledOnce);
        });

        it('should emit `interactionToggle` event on success', (done) => {

            EventBus.$on('interactionToggle', function(){
                done();
            })
            component.vm.sendQuote();

        });

    });

    describe('#reset()', () => {
        
        let component;

        beforeEach(function() {
            component = shallowMount(OutrightLayout, {
                propsData: {
                    marketRequest: test_data.market.market_requests[0]
                },
                methods: {
                    init: () => {}
                },
                localVue
            });
        });

        it('should set `active` state from param', () => {
            

        });

    });

});