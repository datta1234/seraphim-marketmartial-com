import { mount } from '@vue/test-utils';
import StructureSelection from '../../../resources/assets/js/components/ActionBar/Components/RequestMarket/Components/StructureSelectionComponent.vue';
import Market from '../../../resources/assets/js/lib/Market.js';
import { createLocalVue } from '@vue/test-utils';
import BootstrapVue from 'bootstrap-vue';
/*import sinon from 'sinon';
import axios from 'axios';*/

// create an extended `Vue` constructor
const localVue = createLocalVue();
// install boostrap and axios to this vue instance only
localVue.use(BootstrapVue/*, axios*/);

describe('StructureSelectionComponent.vue', () => {
		
	let index_data = {
        market_type_title:'Index Option',
        market_type: {
        	id:1,
        	title:'Index Option',
			markets: [
				new Market({id:1,title:"TOP40",market_type_id:1,is_displayed:1}),
				new Market({id:2,title:"CTOP",market_type_id:1,is_displayed:1}),
				new Market({id:3,title:"CTOR",market_type_id:1,is_displayed:1})
			]
		},
        index_market_object: {},
        number_of_dates: 1,
    };

	beforeEach(function() {
		index_data.index_market_object = {
            market:null,
            trade_structure: '',
            trade_structure_groups: [],
            expiry_dates:[],
            details: null,
        };
	});

	it('Select Structure', (done) => {
		let structureSelectionCallback = (trade_structure) => {
			chai.assert(trade_structure == "Risky", "Risky is the selected structure");
			chai.assert(index_data.index_market_object.trade_structure == "Risky", "The structure has been assigned to the pass data object");
			done();
		};

		const structureSelectionWrapper = mount(StructureSelection, {
			propsData: {
				callback: structureSelectionCallback,
				data: index_data,
				errors:{
					messages:[]
				}
			},
			localVue
		});

		structureSelectionWrapper.vm.selectStructure("Risky");
	});

	it('Load Structures', () => {
		/*const loadStructuresWrapper = mount(StructureSelection, {
			propsData: {
				callback: () => {},
				data: index_data,
				errors:{
					messages:[]
				}
			},
			localVue
		});
		loadStructuresWrapper.vm.loadStructures();*/
		chai.assert(false, "TODO - DO WE TEST THIS?");

	});
});