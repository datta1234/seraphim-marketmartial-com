<template>
    <div dusk="rebates-assigned" class="rebates-assigned" >
        <b-row>
            <b-col cols="6">
                <p>Running total for the year: R{{ splitValHelper( yearly_total, '&nbsp;', 3) }}</p>
            </b-col>
            <b-col cols="6" 
                   class="text-right">
                <label class="mr-sm-2" for="open-interest-expiry">Filter by bank:</label>
                <b-form-select id="open-interest-expiry"
                               class="w-50"
                               :options="bank_filter"
                               v-model="active_bank_filter"
                               @change="setActiveBank">
                </b-form-select>
            </b-col>
        </b-row>
		<div class="card graph-card">
			<div v-if="has_data" class="card-body">
				<bar-graph :chart-data="active_data_set" :options="options"></bar-graph>
			</div>
			<div v-else class="card-body">
				<p class="text-center">No Data for this market to display</p>
			</div>
		</div>
    </div>
</template>

<script>
	import BarGraph from '../../BarGraph.js';
    export default {
    	components: {
	      	BarGraph
	    },
    	props: {
            'market_data': {
                type: Object
            },
            'yearly_total': {
                type: Number
            },
        },
        data() {
            return {
            	graph_data:null,
            	has_data: false,
            	active_data_set: {
            		labels: [],
            		datasets:[
                        {
                            id_key: "TOP40",
                            backgroundColor:'#96e3d7',
                            label: "TOP40",
                            data:[]
                        },
                        {
                            id_key: "DTOP",
                            backgroundColor:'#269a9b',
                            label: "DTOP",
                            data:[]
                        },
                        {
                            id_key: "DCAP",
                            backgroundColor:'#1a333f',
                            label: "DCAP",
                            data:[]
                        },
                        {
                            id_key: "SINGLES",
                            backgroundColor:'#f3931c',
                            label: "SINGLES",
                            data:[]
                        },
                    ]
            	},
                bank_filter: [
                    {text: "All Banks", value: null},
                ],
            	active_bank_filter: null,
				options: {
					scales: {
				        yAxes: [{
				            display: true,
				            ticks: {
				                beginAtZero: true,  // minimum value will be 0.
                                callback: this.formatLargeNumberAxis
				            }
				        }],
                        xAxes: [{
                            display: true,
                            ticks: {
                                autoSkip: false
                            }
                        }],
				    },
                    tooltips: {
                        callbacks: {
                            label: this.formatToolTipLabel,
                        }
                    },
					responsive: true, 
					maintainAspectRatio: false
				}
            };
        },
        methods: {
            formatLargeNumberAxis(value, index, values) {
                return this.$root.splitValHelper(value, ',', 3);
            },
            formatToolTipLabel(tooltipItem, data) {
                return this.$root.splitValHelper(tooltipItem.yLabel, ',', 3);
            },
            setChartData(data) {
            	if(data == null || Object.keys(data).length === 0) {
            		this.has_data = false;
            		return;
            	}

            	this.has_data = true;
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
                this.active_data_set.datasets.forEach(dataset => {
                    dataset.data = this.setData(dataset.id_key, data); 
                });
            },
            setData(id_key, data) {
                let temp_array = [];
                if(this.active_bank_filter) {
                    temp_array.push(data[id_key]);
                } else {
                    Object.keys(data).forEach(organisation => {
                        temp_array.push(data[organisation][id_key]);
                    });
                }
                return temp_array;
            },
            setActiveBank(bank) {
                this.has_data = false;
                this.active_bank_filter = bank;
                this.setChartData(this.active_bank_filter ? this.graph_data[this.active_bank_filter] : this.graph_data);
            },
            setOrganisations(data) {
                if(Object.keys(this.graph_data).length > 0) {
                    Object.keys(this.graph_data).forEach( organisation => {
                        this.bank_filter.push({text: organisation, value: organisation});
                    });
                }
            },
        },
        mounted() {
        	this.graph_data = this.market_data;
            this.setOrganisations(this.graph_data);
            this.setChartData(this.graph_data);
        }
    }
</script>