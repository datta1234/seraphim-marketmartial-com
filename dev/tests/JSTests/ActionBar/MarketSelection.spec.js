import { mount } from '@vue/test-utils';
import MarketSelection from '../../../resources/assets/js/components/ActionBar/Components/RequestMarket/Components/MarketSelectionComponent.vue';
import Market from '../../../resources/assets/js/lib/Market.js';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';

// create an extended `Vue` constructor
const localVue = createLocalVue();
// install boostrap to this vue instance only
localVue.use(BootstrapVue);

describe('MarketSelectionComponent.vue', () => {
	let index_data = {
        market_type_title:'Index Option',
        market_type: {
			markets: [
				new Market({id:1,title:"TOP40",market_type_id:1,is_displayed:1}),
				new Market({id:2,title:"CTOP",market_type_id:1,is_displayed:1}),
				new Market({id:3,title:"CTOR",market_type_id:1,is_displayed:1})
			]
		},
        index_market_object: {

            market:null,
            trade_structure: '',
            trade_structure_groups: [],
            expiry_dates:[],
            details: null,

        },
        number_of_dates: 1,
    };

	it('Select Market', (done) => {
		let marketSelectionCallback = (market) => {
			chai.assert.deepEqual(index_data.index_market_object.market, market, "CTOP is the selected Market set");
			done();
		};

		const indexSelectionWrapper = mount(MarketSelection, {
			propsData: {
				callback: marketSelectionCallback,
				data: index_data,
				errors:{
					messages:[]
				}
			},
			localVue
		});

		chai.assert.equal(index_data.index_market_object.market, null, "There is no selected market");
		indexSelectionWrapper.find('#'+index_data.market_type.markets[1].title+'-market-choice').trigger('click');

	});
});