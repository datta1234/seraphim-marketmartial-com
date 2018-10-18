<template>
    <div dusk="rebates-earned" class="rebates-earned" >
        <b-row>
            <b-col cols="6">
                <p>Running total for the year: R{{ splitValHelper( yearly_total, '&nbsp;', 3) }}</p>
            </b-col>
            <b-col cols="6" 
                   class="text-right">
                <label class="mr-sm-2" for="open-interest-expiry">Filter by user:</label>
                <b-form-select id="open-interest-expiry"
                               class="w-50"
                               :options="users_filter"
                               v-model="active_user_filter"
                               @change="setActiveUser">
                </b-form-select>
            </b-col>
        </b-row>
		<div class="card graph-card">
			<div v-if="has_data" class="card-body">
				<bar-graph :chart-data="active_data_set" :options="options"></bar-graph>
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
	import BarGraph from '../BarGraph.js';
    export default {
    	components: {
	      	BarGraph
	    },
    	props: {
            'authed_user': {
                type: String
            },
            'market_data': {
                type: Object
            },
            'users': {
                type: Array
            },
            'yearly_total': {
                type: Number
            },
        },
        data() {
            return {
            	graph_data:null,
            	my_trades_only: false,
            	has_data: false,
            	active_data_set: {
            		labels: [],
            		datasets:[
                        {
                            market: "TOP40",
                            backgroundColor:'#96e3d7',
                            label: "TOP40",
                            data:[]
                        },
                        {
                            market: "DTOP",
                            backgroundColor:'#269a9b',
                            label: "DTOP",
                            data:[]
                        },
                        {
                            market: "DCAP",
                            backgroundColor:'#1a333f',
                            label: "DCAP",
                            data:[]
                        },
                        {
                            market: "SINGLES",
                            backgroundColor:'#f3931c',
                            label: "SINGLES",
                            data:[]
                        },
                    ]
            	},
                users_filter: [
                    {text: "All Users", value: null},
                ],
            	active_user_filter: null,
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
            setChartData(data) {
            	if(data == null) {
            		this.has_data = false;
            		return;
            	}
            	this.has_data = true;
            	this.active_data_set.labels = [];
                this.setLabels(data);
                this.setData(data);
            },
            setActiveUser(user) {
                this.active_user_filter = user;
                this.has_data = false;
                this.setChartData(this.graph_data);
            },
            setLabels(data) {
                Object.keys(data).forEach(date => {
                    if(this.active_data_set.labels.indexOf(moment(date,'MMMYY').format('MMMYY'))
                        === -1) {
                        this.active_data_set.labels.push(
                            moment(date,'MMMYY').format('MMMYY')
                        );
                    }
                });
                this.$root.dateStringArraySort(this.active_data_set.labels, 'MMMYY');
            },
            setData(data) {
                this.active_data_set.datasets.forEach(set => {
                    set.data = [];
                    this.active_data_set.labels.forEach(date => {
                        // The market exists in the data under the date
                        if(data[date][set.market]) {
                            let total = 0;
                            Object.keys(data[date][set.market]).forEach(user => {
                                if(this.active_user_filter) {
                                    total = (this.active_user_filter == user) ?
                                        total + data[date][set.market][user] : total;
                                } else {
                                    total += data[date][set.market][user];
                                }
                            });
                            set.data.push(total);
                        } else {
                            set.data.push(0);
                        }
                    });                    
                });
            },
	        toggleMyTrades(checked) {
                this.setActiveUser(checked ? this.authed_user : null);
	        },
            populateUsers() {
                if(this.users) {
                    this.users.forEach( user => {
                        this.users_filter.push({text: user, value: user});
                    });
                }
            },
        },
        mounted() {
        	this.graph_data = this.market_data;
            this.populateUsers();
            this.setChartData(this.graph_data);
        }
    }
</script>