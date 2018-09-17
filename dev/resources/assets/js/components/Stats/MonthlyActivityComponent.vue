<template>
    <div dusk="monthly-activity" class="monthly-activity" >
    	<div v-for="(data,market) in graph_data" class="btn-group" role="group" aria-label="Basic example">
		  	<button @click="setDataSet(data)" type="button" class="btn btn-secondary mr-2">{{ market }}</button>
		</div>
		<bar-graph v-if="active_data_set !== null" :data="active_data_set" :options="options"></bar-graph>
		<p v-else>No Data for this market to display</p>
    </div>
</template>

<script>
	import BarGraph from './BarGraph.js';
    export default {
    	components: {
	      	BarGraph
	    },
    	props: {
            'graph_data': {
                type: Object
            },
        },
        data() {
            return {
            	active_data_set: null,
            	test: {
				  labels: ['January', 'February'],
				  datasets: [
				    {
				      label: 'GitHub Commits',
				      backgroundColor: '#f87979',
				      data: [40, 20]
				    },
				    {
				      label: 'something',
				      backgroundColor: '#f87979',
				      data: [80, 30]
				    }
				  ]
				},
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
            setDataSet(data) {
            	console.log(data);
            	if(data == null) {
            		this.active_data_set = null;
            		return;
            	}
            	this.active_data_set = {
            		lables: [],
            		datasets:[]
            	}

            	/*Object.keys(data).forEach(date => {
            		this.active_data_set.labels.push(date);
		            
		            Object.keys(data[date]).forEach(key => {
	            		this.active_data_set.datasets.push({
							label: key,
							backgroundColor: '#f87979',
							data: [80, 30]
	            		});
			            data[key]
			        });
		        });
            	for (let dates in $data) {
	            	this.active_data_set = 	{
					  labels: ['TESTING', 'February'],
					  datasets: [
					    {
					      backgroundColor: '#f87979',
					      data: [40, 20]
					    }
					  ]
					}
            	}*/

            }
        },
        mounted() {
            console.log(this.graph_data);
            this.active_data_set = this.test;
        }
    }
</script>