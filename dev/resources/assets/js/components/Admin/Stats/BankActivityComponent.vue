<template>
    <div dusk="bank-activity" class="bank-activity" >
    	<div v-for="(data,market) in graph_data" class="btn-group" role="group">
		  	<button v-bind:class="{ active: active_market == market }" 
                    @click="setChartData(data,market,false)" 
                    type="button" 
                    class="btn mm-button mr-1 card-tab-button mm-nav-button">
                {{ market }}
            </button>
		</div>
		<div class="card graph-card">
			<div v-if="has_data" class="card-body">
                <bar-graph :chart-data="active_data_set" :options="options"></bar-graph>
                <b-row class="mt-4">
                    <b-col cols="4" offset="8" class="text-right">
                        <label class="mr-sm-2" for="bank-select-filter">Filter by Bank:</label>
                        <b-form-select id="bank-select-filter"
                                   class="w-50"
                                   :options="bank_filter"
                                   v-model="active_bank_filter"
                                   @change="setActiveBank">
                        </b-form-select>
                    </b-col>
                </b-row>
			</div>
			<div v-else class="card-body">
				<p class="text-center">No Data for this market to display</p>
			</div>
		</div>
    </div>
</template>

<script>
	import BarGraph from '~/components/BarGraph.js';
    export default {
    	components: {
	      	BarGraph
	    },
    	props: {
            'market_data': {
                type: Object
            },
            'labels': {
            	type: Object
            }
        },
        data() {
            return {
            	graph_data:null,
            	data_details:[
            		{
                        id_key: "total",
            			backgroundColor:'#96e3d7',
            			label: "Number of trades",
                        data: [],
            		},
            		{
                        id_key: "traded",
            			backgroundColor:'#269a9b',
            			label: "Markets Made (Traded)",
                        data: [],
            		},
            		{
                        id_key: "traded_away",
            			backgroundColor:'#1a333f',
            			label: "Markets Made (Traded Away)",
                        data: [],
            		}
            	],
            	has_data: false,
            	active_data_set: {
            		labels: [],
            		datasets:[]
            	},
            	active_market: '',
                active_bank_filter: null,
                bank_filter: [
                    {text: "All Banks", value: null},
                ],
				options: {
					scales: {
				        yAxes: [{
				            display: true,
				            ticks: {
				                beginAtZero: true   // minimum value will be 0.
				            }
				        }],
                        xAxes: [{
                            display: true,
                            ticks: {
                                autoSkip: false
                            }
                        }],
				    },
					responsive: true, 
					maintainAspectRatio: false
				}
            };
        },
        methods: {
            setChartData(data, market, is_filter) {
                if(data == null) {
                    this.has_data = false;
                    return;
                }
            	
                this.has_data = true;
                
                if(!is_filter) {
                    this.active_bank_filter = null; 
                    this.active_market = market;
                    this.bank_filter = [{text: "All Banks", value: null}],
                    this.setOrganisations(Object.keys(data));
                }
                this.setLabels(data);
                this.setDataSet(data);
            },
            setLabels(data) {
                if(this.active_bank_filter){
                    this.active_data_set.labels = [this.active_bank_filter];
                } else {
                    this.active_data_set.labels = Object.keys(data); 
                }
            },
            setDataSet(data) {
                // Clear data set
                this.data_details.forEach(dataset => {
                    dataset.data = this.setData(dataset.id_key, data); 
                });
                this.active_data_set.datasets = this.data_details;
            },
            setData(id_key, data) {
                let temp_array = [];
                if(this.active_bank_filter) {
                    temp_array.push(data[this.active_bank_filter][id_key]);
                } else {
                    Object.keys(data).forEach(organisation => {
                        temp_array.push(data[organisation][id_key]);
                    });
                }
                return temp_array;
            },
            setOrganisations(banks) {
                banks.forEach(bank =>{
                    this.bank_filter.push({text: bank, value: bank})
                });
            },
            setActiveBank(bank) {
                this.has_data = false;
                this.active_bank_filter = bank;
                this.setChartData(this.graph_data[this.active_market],this.active_market, true);
            },
        },
        mounted() {
        	this.graph_data = this.market_data; 
            this.setChartData(this.graph_data['TOP40'],'TOP40', false);
        }
    }
</script>