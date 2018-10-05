<template>
    <div dusk="open-interests" class="open-interests" >
        <b-row>
            <b-col cols="6">
                <div v-for="(market,index) in graph_markets" class="btn-group" role="group">
                    <button v-bind:class="{ active: active_market == market }" 
                            @click="setChartData(graph_data[market],market)" 
                            type="button" 
                            class="btn mm-button mr-1 card-tab-button mm-nav-button">
                        {{ market }}
                    </button>
                </div>
            </b-col>
            <b-col cols="3" class="text-right" v-if="active_data_set.filter_contracts.length > 1">
                <label class="mr-sm-2" for="open-interest-expiry">Contract:</label>
                <b-form-select id="open-interest-expiry"
                               class="w-50"
                               :options="contractList"
                               v-model="active_contract"
                               @change="setActiveContact">
                </b-form-select>
            </b-col>
            <b-col cols="3" 
                   :offset="active_data_set.filter_contracts.length > 1 ? 0 : 3" 
                   class="text-right">
                <label class="mr-sm-2" for="open-interest-expiry">Expiration:</label>
                <b-form-select id="open-interest-expiry"
                               class="w-50"
                               :options="expirationDates"
                               v-model="active_date"
                               @change="setActiveDate">
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
        computed: {
            expirationDates: function() {
                return this.active_data_set.filter_dates;
            },
            contractList: function() {
                return this.active_data_set.filter_contracts;
            }
        },
        data() {
            return {
                graph_markets: ['ALSI','DTOP','DCAP','SINGLES'],
            	graph_data:null,
                data_details:[
                    {
                        label: 'Call Delta',
                        backgroundColor:'rgba(243, 147, 28, 1)',
                        data: [],
                        fill: false,
                        type: 'line',
                        showLine: false,
                        yAxisID: 'Delta'
                    },
                    {
                        label: 'Put Delta',
                        backgroundColor:'rgba(243, 147, 28, 1)',
                        data: [],
                        fill: false,
                        type: 'line',
                        showLine: false,
                        yAxisID: 'Delta'
                    },
                    {
                        label: 'Put',
                        backgroundColor:'rgba(26, 51, 63, 1)',
                        data: [],
                        yAxisID: 'OpenInterests'
                    },
                    {
                        label: 'Call',
                        backgroundColor:'rgba(26, 51, 63, 1)',
                        data: [],
                        yAxisID: 'OpenInterests'
                    },
                ],
                has_data: true,
                active_data_set: {
                    filter_contracts: [],
                    filter_dates: [],
                    labels: [],
                    datasets:[]
                },
                active_market: '',
                active_contract: '',
                active_date: '',
                options: {
                    scales: {
                        yAxes: [
                            {
                                id: 'OpenInterests',
                                display: true,
                                ticks: {
                                    scaleVal: 1000,
                                    min: -15000,
                                    max: 15000,
                                    stepSize: 3000,
                                    beginAtZero: true   // minimum value will be 0.
                                }
                            },
                            {
                                id: 'Delta',
                                display: true,
                                position: 'right',
                                ticks: {
                                    min: -100,
                                    max: 100,
                                    stepSize: 20,
                                    beginAtZero: true   // minimum value will be 0.
                                },
                            },
                        ],
                        xAxes: [
                            {
                                display: true,
                                gridLines: {
                                    display:false
                                },
                                stacked: true
                            }
                        ]
                    },
                    responsive: true, 
                    maintainAspectRatio: false,
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
                this.active_data_set.filter_dates = [];
                this.setContract(data);
            },
            setContract(market_data) {
                this.active_data_set.filter_contracts = Object.keys(market_data).map(x => x).sort();
                this.setActiveContact(this.active_data_set.filter_contracts[0]);
            },
            setActiveContact(contract) {
                this.active_contract = contract;
                let data = this.graph_data[this.active_market][contract];
                this.setDates(data);
            },
            setDates(contact_data) {
                this.active_data_set.filter_dates = Object.keys(contact_data).map(x => x);
                this.$root.dateStringArraySort(this.active_data_set.filter_dates, 'YYYY-MM-DD');
                this.setActiveDate(this.active_data_set.filter_dates[0]);
            },
            setActiveDate(date) {
                this.active_date = date;
                let data = this.graph_data[this.active_market][this.active_contract][date];
                this.setLabels(data);
                this.setDataSet(data);
            },
            setLabels(date_data) {
                this.active_data_set.labels = [];
                this.active_data_set.labels = Object.keys(date_data).map(x => parseFloat(x)).sort( (a,b) => {
                    if (a < b) {
                        return -1;
                    }
                    if (a > b) {
                        return 1;
                    }
                    // a must be equal to b
                    return 0;
                });
            },
            setDataSet(date_data) {
                let temp_data_set = [];
                this.data_details.forEach(dataset => {
                    dataset.data = []; 
                })
                Object.keys(date_data).forEach(strike => {
                    let obj = {
                        'Put': null,
                        'Put Delta': null,
                        'Call': null,
                        'Call Delta': null,
                    };
                    date_data[strike].forEach(single => {
                        if(single.is_put) {
                            obj['Put'] = single.open_interest;
                            obj['Put Delta'] = (single.delta * 100);
                        } else {
                            obj['Call'] = single.open_interest;
                            obj['Call Delta'] = (single.delta * 100);
                        }
                    });
                    this.data_details.forEach(dataset => {
                        dataset.data.push(obj[dataset.label]); 
                    });
                });
                this.data_details.forEach(data_set => {
                    let scale = this.options.scales.yAxes.find(x => x.id == data_set.yAxisID).ticks;
                    if(scale.scaleVal) {
                        let max = Math.ceil(
                            Math.max(
                                Math.abs(Math.min(...data_set.data)),
                                Math.abs(Math.max(...data_set.data))
                            ) / scale.scaleVal
                        ) * scale.scaleVal;
                        max = max < scale.scaleVal ? scale.scaleVal : max;
                        scale.min = -1 * max;
                        scale.max = max;
                        scale.stepSize = max / 5;
                    }
                    temp_data_set.push(data_set);
                });
                this.active_data_set.datasets = temp_data_set;
            },
        },
        mounted() {
            this.graph_data = this.data;
            this.setChartData(this.graph_data['ALSI'],'ALSI');
        }
    }
</script>