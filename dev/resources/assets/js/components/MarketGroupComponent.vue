<template>
    <div dusk="market-group" class="user-market">
        <div class="container-fluid h-100">
            <div class="row h-100">
                <div class="col-12">
                    <h2 class="text-center market-group-title">{{ market.title }}</h2> 
                </div>
                
                <div v-if="this.is_delta" class="user-market-group col-12">
                    <!-- Date collection section -->
                    <div class="row mt-3 pr-3 pl-3" v-for="group in market_delta_groups_order">
                        <div class="col-12" v-if="market_delta_groups[group] && market_delta_groups[group].length > 0">
                            <p class="mb-1">{{ group }}</p>
                        </div>
                        <market-tab :market-request="m_req" :key="m_req.id" v-for="(m_req,m_req_index) in market_delta_groups[group]" :no_cares="no_cares"></market-tab>
                    </div><!-- END Date collection section -->
                </div>

                <div v-else class="user-market-group col-12">
                    <!-- Date collection section -->
                    <div class="row mt-3 pr-3 pl-3" v-for="date in market_date_groups_order">
                        <div class="col-12">
                            <p class="mb-1">{{ date }}</p>
                        </div>
                        <market-tab :market-request="m_req" :key="m_req.id" v-for="(m_req,m_req_index) in market_date_groups[date]" :no_cares="no_cares"></market-tab>
                    </div><!-- END Date collection section -->
                </div>

                <div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Market from '../lib/Market';
    import moment from 'moment';
    import { EventBus } from '~/lib/EventBus';

    export default {
        props: {
            'market': {
                type: Market
            },
            'no_cares':{
                type: Array
            }
        },
        watch: {
            'market.market_requests': function (nV) {
                this.updateRequests(nV);
            }
        },
        data() {
            return {
                market_delta_groups: {},
                market_delta_groups_order: [
                    this.$root.config("trade_structure.efp_switch.title"),
                    this.$root.config("trade_structure.efp.title"),
                    this.$root.config("trade_structure.rolls.title"),
                ],

                market_date_groups: {},
                market_date_groups_order: [],

                groupings: {
                    default: 'trade_items.default.expiration_date',
                    delta: 'trade_structure'
                }
            };
        },
        computed: {
            'is_delta':function() {
                return this.market.id == this.$root.config('app.market_ids.delta_one');
            }
        },
        methods: {
            /*
            * General
            */
            getAttr(obj, key) {
                return key.split('.').reduce((acc, cur) => {
                    if(acc && typeof acc[cur] !== 'undefined') {
                        return acc[cur];
                    }
                    return undefined;
                }, obj);
            },
            mapGroups: function(markets, grouping) {
                // map markets to dates
                let market_requests = markets.reduce((x,y) => {
                    let key = this.getAttr(y, grouping) ? this.getAttr(y, grouping) : '';
                    if(!x[key]) { 
                        x[key] = [];
                    }
                    x[key].push(y);
                    return x;
                }, {});
                // sort by strike
                Object.keys(market_requests).forEach( (date) => {
                    market_requests[date].sort( (a, b) => {
                        return a.trade_items.default.Strike - b.trade_items.default.Strike;
                    });
                });
                return market_requests;
            },
            /*
            *   Default Group Methods
            */
            sortDefaultGroups: function(unsorted_date_groups) {
                let dates = [];
                Object.keys(unsorted_date_groups).forEach( (date) => {
                    dates.push(date);
                });
                if(dates.length > 0) {
                    this.$root.dateStringArraySort(dates, 'MMMYY');
                }
                return dates;
            },
            /*
            *   Delta Group Methods
            */
            sortDeltaGroups: function(unsorted_delta_groups) {
                let dates = [];
                Object.keys(unsorted_delta_groups).forEach( (date) => {
                    dates.push(date);
                });
                if(dates.length > 0) {
                    this.$root.dateStringArraySort(dates, 'MMMYY');
                }
                return dates;
            },
            /*
            *   Update Methods
            */
            updateDeltaRequests(reqs) {
                this.market_delta_groups = this.mapGroups(reqs, this.groupings.delta);
            },
            updateDefaultRequests(reqs) {
                this.market_date_groups = this.mapGroups(reqs, this.groupings.default);
                this.market_date_groups_order = this.sortDefaultGroups(this.market_date_groups);
            },
            updateRequests(reqs) {
                switch(this.market.id) {
                    case this.$root.config('app.market_ids.delta_one'):
                        return this.updateDeltaRequests(reqs);
                    break;
                    default:
                        return this.updateDefaultRequests(reqs);
                }
            }
        },
        mounted() {
            this.updateRequests(this.market.market_requests);
            EventBus.$on('display-update-forced', () => this.updateRequests(this.market.market_requests));
        }
    }
</script>