<template>
    <div dusk="open-interests" class="open-interests" >
        <div v-for="(market,index) in graph_markets" class="btn-group" role="group">
            <button v-bind:class="{ active: active_market == market }" 
                    @click="setChartData(graph_data[market],market)" 
                    type="button" 
                    class="btn mm-button mr-1 card-tab-button mm-nav-button">
                {{ market }}
            </button>
        </div>
        <div class="card graph-card">
            <div v-if="has_data" class="card-body">
                <bar-graph :data="active_data_set" :options="options"></bar-graph>
            </div>
            <div v-else class="card-body">
                <p class="text-center">No Data for this market to display</p>
            </div>
        </div>
    </div>
</template>

<script>
    import BarGraph from './Components/BarGraph.js';
    export default {
        components: {
            BarGraph
        },
    	props: {
            'data': {
                type: Object
            },
        },
        data() {
            return {
                graph_markets: ['ALL','ALSI','DTOP','DCAP','SINGLES'],
            	graph_data:null,
                data_details:{
                    putt:{
                        backgroundColor:'#96e3d7',
                        label: 'Putt'
                    },
                    call:{
                        backgroundColor:'#269a9b',
                        label: 'Call'
                    },
                    delta:{
                        backgroundColor:'#1a333f',
                        label: 'Delta',
                        type: 'line',
                    }
                },
                has_data: true,
                active_data_set: {
                    labels: ['11','12','15'],
                    datasets:[
                        {
                            label: 'Putt',
                            backgroundColor:'rgba(157, 228, 216, 0.8)'/*#96e3d7*/,
                            data: [5, 50, 35],
                        },
                        {
                            label: 'Call',
                            backgroundColor:'rgba(51, 155, 156, 0.8)'/*#269a9b*/,
                            data: [-10, 42, 25],
                        },
                        {
                            label: 'Delta',
                            backgroundColor:'rgba(27, 47, 61, 1)'/*#1a333f*/,
                            data: [50, 14, 32],
                            type: 'line',
                            options: {
                                fill: false
                            }
                        },
                    ]
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
                    maintainAspectRatio: false,
                    plugins: {
                        filler: {
                            propagate: true
                        }
                    }
                }
            };
        },
        methods: {
            setChartData(data, market) {
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
            },
            setLabels(data) {
                console.log("=====================setLabels=====================");

                this.active_data_set.labels = Object.keys(data).map(x => parseFloat(x)).sort( (a,b) => {
                    if (a < b) {
                        return -1;
                    }
                    if (a > b) {
                        return 1;
                    }
                    // a must be equal to b
                    return 0;
                });
                console.log(this.active_data_set);
                console.log("===================END setLabels===================");  
            },
            setDataSet(data) {
                for (let i = this.active_data_set.labels.length - 1; i >= 0; i--) {
                    let item = data[this.active_data_set.labels[i]];
                    for (var j = item.length - 1; j >= 0; j--) {
                        item[j]
                    }
                }
                /*Object.keys(data).forEach(set => {
                    this.active_data_set.datasets.push({
                        label: this.data_details[set].label,
                        backgroundColor: this.data_details[set].backgroundColor,
                        data: this.setData(set, data),
                    });
                });*/
            },
            setData(set, data) {
                /*let count_array = [];
                this.active_data_set.labels.forEach(date => {
                    let found = data[set].find(element => {
                        return moment(element.month,'M-YYYY').isSame(moment(date,'MMM YY'))
                    });
                    if(typeof found != 'undefined') {
                        count_array.push(found.total);
                    } else {
                        count_array.push(null);
                    }
                });
                return count_array;*/
            },
        },
        mounted() {
        	console.log(this.data);
            //this.graph_data = this.data;
            //this.setChartData(this.graph_data['ALL'],'ALL');
        }
    }
</script>