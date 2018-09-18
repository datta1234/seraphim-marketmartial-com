<template>
    <div dusk="monthly-activity" class="monthly-activity" >
    	<div v-for="(data,market) in graph_data" class="btn-group" role="group" aria-label="Basic example">
		  	<button @click="setChartData(data,market)" type="button" class="btn btn-secondary mr-2">{{ market }}</button>
		</div>
		<div class="card graph-card">
			<div v-if="has_data" class="card-body">
				<bar-graph :data="active_data_set" :options="options"></bar-graph>
				<b-form-checkbox class="float-right mt-3" @change="toggleMyTrades" v-model="my_trades_only">
	    			Show only my trades
			    </b-form-checkbox>
			</div>
			<div v-else class="card-body">
				<p class="text-center">No Data for this market to display</p>
			</div>
		</div>
    </div>
</template>

<script>
	import BarGraph from './BarGraph.js';
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
            	my_trades_only: false,
            	data_details:{
            		total_trades:{
            			backgroundColor:'#96e3d7',
            			label: "Number of trades"
            		},
            		traded:{
            			backgroundColor:'#269a9b',
            			label: "Markets Made (Traded)"
            		},
            		traded_away:{
            			backgroundColor:'#1a333f',
            			label: "Markets Made (Traded Away)"
            		}
            	},
            	has_data: false,
            	active_data_set: {
            		labels: [],
            		datasets:[]
            	},
            	active_market: '',
				options: {
					scales: {
				        yAxes: [{
				            display: true,
				            ticks: {
				                beginAtZero: true   // minimum value will be 0.
				            }
				        }]
				    },
					responsive: true, 
					maintainAspectRatio: false
				}
            };
        },
        methods: {
            setChartData(data, market) {
            	console.log("Market: ",market,'    ',"Setting this data: ",data);
            	this.active_market = market;
            	if(data == null) {
            		this.has_data = false;
            		return;
            	}
            	this.has_data = true;
            	this.active_data_set.labels = [];
            	this.active_data_set.datasets = [];
            	this.setLabels(data);
            	this.setDataSet(data);
            	console.log("Chart Data",this.active_data_set);
            },
            setLabels(data) {
            	Object.keys(data).forEach(set => {
            		Object.keys(data[set]).forEach(date => {
            			if(this.active_data_set.labels.indexOf(date) === -1) {
            				this.active_data_set.labels.push(date);
            			}
		        	});
		        });
		    	this.dateStringArraySort(this.active_data_set.labels);  
            },
            setDataSet(data) {
            	Object.keys(data).forEach(set => {
            		this.active_data_set.datasets.push({
            			label: this.data_details[set].label,
				      	backgroundColor: this.data_details[set].backgroundColor,
				      	data: this.setData(set, data),
            		});
		        });
            },
            setData(set, data) {
            	let count_array = [];
            	this.active_data_set.labels.forEach(date => {
            		if(typeof data[set][date] != 'undefined') {
            			count_array.push(data[set][date]);
            		} else {
            			count_array.push(null);
            		}
            	});
            	return count_array;
            },
            dateStringArraySort(date_string_array) {
	            for(let i = 0; i < date_string_array.length - 1; i++) {
	                for(let j = 0; j < date_string_array.length - i - 1; j++) {
	                    if( moment(date_string_array[j+1],'MMM YYYY').isBefore(moment(date_string_array[j],'MMM YYYY')) ) {
	                        let temp = date_string_array[j];
	                        date_string_array[j] = date_string_array[j+1];
	                        date_string_array[j+1] = temp;
	                    }
	                }
	            }
	        },
	        toggleMyTrades(checked) {
	        	console.log(checked);
	        	axios.get(axios.defaults.baseUrl + 'my-activity', {
	        		params:{
	        			'my_trades': checked ? "1" : "0"
	        		}
	        	})
	            .then(activityResponse => {
	                if(activityResponse.status == 200) {
	                    console.log(activityResponse);
	                    this.graph_data = activityResponse.data;
	        			this.setChartData(activityResponse.data[this.active_market],this.active_market);    
	                } else {
	                	this.$toasted.error("Failed to load Your Trades.")
	                    console.error(err);    
	                }
	            }, err => {
	                console.error(err);
	            });
	        },
        },
        mounted() {
        	this.graph_data = this.market_data;
            this.setChartData(this.graph_data['TOP40'],'TOP40');
        }
    }
</script>