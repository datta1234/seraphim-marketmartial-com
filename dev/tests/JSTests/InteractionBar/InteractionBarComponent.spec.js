import { mount } from '@vue/test-utils';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

import Market from '~/lib/Market.js';
import UserMarketRequest from '~/lib/UserMarketRequest.js';
import InteractionBarComponent from '~/components/InteractionBarComponent.vue';
import { EventBus } from '~/lib/EventBus.js';

// create an extended `Vue` constructor
const localVue = createLocalVue();
// install boostrap to this vue instance only
localVue.use(BootstrapVue);

describe('InteractionBarComponent.vue', () => {

    let test_data;

    before(function(){
        test_data = {
            market_request: new UserMarketRequest({id: "1"})
        };
    });

    describe('#toggleBar()', () => {
        
        let component;

        beforeEach(function(){
            component = mount(InteractionBarComponent, {
                localVue
            });
        });

        it('should set `active` state from param', () => {
            
            component.vm.toggleBar(true, test_data.market_request);
            chai.assert.equal(component.vm.opened, true);

        });

        it('should set `market_request` attribute from param', () => {
            
            component.vm.toggleBar(true, test_data.market_request);
            chai.assert.equal(component.vm.market_request, test_data.market_request);

        });

        it('should fire  `interactionChange` Event On OPEN', (done) => {
            
            EventBus.$on('interactionChange', function(){
                done();
            });
            component.vm.toggleBar(true, test_data.market_request);

        });

        it('should fire  `interactionClose` Event On CLOSE', (done) => {
            
            EventBus.$on('interactionClose', function(){
                done();
            });
            component.vm.toggleBar(false);

        });

    });

});