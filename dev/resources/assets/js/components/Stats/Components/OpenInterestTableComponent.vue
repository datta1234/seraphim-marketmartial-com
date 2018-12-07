<template>
    <div dusk="open-interests-table" class="open-interests-table" >
        <div class="card mt-5">
            <div class="card-header text-center">
                <h2 class="mt-2 mb-2">Open Interests Data</h2>
            </div>
            <div class="card-body">
                <b-row>
                    <b-col cols="12">
                        <b-form v-on:submit.prevent="" id="chat-message-form">
                            <b-row class="mt-2">
                                <b-col cols="1">
                                    <label class="mr-sm-2" for="stats-safex-markets">Markets:</label>
                                </b-col>
                                <b-col cols="3">
                                    <b-form-select id="stats-safex-table"
                                               class="w-100"
                                               :options="markets_filter"
                                               v-model="table_data.param_options.market">
                                    </b-form-select>
                                </b-col>
                            </b-row>
                            <b-row class="mt-2">
                                <b-col cols="1">
                                    <label class="mr-sm-2" for="stats-open-interests-search">Search:</label>
                                </b-col>
                                <b-col cols="3">
                                    <b-input v-model="table_data.param_options.search" 
                                             class="w-100 mr-0" 
                                             id="stats-open-interests-search" 
                                             placeholder="Search..." />
                                </b-col>
                                <b-col cols="2">
                                    <button type="submit" 
                                            class="btn mm-button w-100 float-right ml-0 mr-2" 
                                            @click="loadTableData()">
                                        Filter
                                    </button>
                                </b-col>
                                <b-col cols="4" offset="2">
                                    <datepicker v-model="table_data.param_options.expiration"
                                                class="float-right filter-date-picker"
                                                name="open-interests-table-datepicker"
                                                placeholder="Select a date"
                                                :bootstrap-styling="true"
                                                :calendar-button="true"
                                                calendar-button-icon="fas fa-calendar-alt"
                                                :clear-button="true"
                                                clear-button-icon="fas fa-trash-alt">    
                                    </datepicker>
                                </b-col>
                            </b-row>
                        </b-form>
                    </b-col>
                </b-row>
                <template v-if="table_data.data.length > 0">
                    <!-- Main table element -->
                    <b-table v-if="table_data.loaded && table_data.data != null"
                             class="mt-2 stats-table"
                             stacked="md"
                             :items="table_data.data"
                             :fields="table_data.table_fields"
                             :sort-by.sync="table_data.param_options.order_by"
                             :sort-desc.sync="table_data.param_options.order_ascending"
                             :no-local-sorting="true"
                             @sort-changed="sortingChanged">
                        <template v-for="(field,key) in table_data.table_fields" :slot="field.key" slot-scope="row">
                            {{ formatItem(row.item, field.key) }}
                        </template>
                    </b-table>

                    <b-row v-if="table_data.data != null" class="justify-content-md-center">
                        <b-col md="auto" class="my-1">
                            <b-pagination @change="changePage($event)"
                                          :total-rows="table_data.pagination.total"
                                          :per-page="table_data.pagination.per_page"
                                          :hide-ellipsis="true"
                                          v-model="table_data.pagination.current_page"
                                          align="center"/>
                        </b-col>
                    </b-row>
                </template>
                <template v-else>
                    <p class="text-center mt-5">No Open Interest Data to display</p>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
    	props: {
            
        },
        data() {
            return {
                markets_filter: [
                    {text: "All Markets", value: null},
                    {text: "ALSI", value: "ALSI"},
                    {text: "DTOP", value: "DTOP"},
                    {text: "DCAP", value: "DCAP"},
                    {text: "SINGLES", value: "SINGLES"},
                ],
                table_data: {
                    table_fields: [
                        { key: 'market_name', label: 'Market Name', sortable: true, sortDirection: 'desc' },
                        { key: 'contract', label: 'Contract', sortable: true, sortDirection: 'desc' },
                        { key: 'expiry_date', label: 'ExpiryDate', sortable: true, sortDirection: 'desc' },
                        { key: 'is_put', label: 'Put/Call', sortable: true, sortDirection: 'desc' },
                        { key: 'open_interest', label: 'Strike Price', sortable: true, sortDirection: 'desc' },
                        { key: 'strike_price', label: 'Open Interest', sortable: true, sortDirection: 'desc' },
                        { key: 'delta', label: 'Delta', sortable: true, sortDirection: 'desc' },
                        { key: 'spot_price', label: 'Spot Price', sortable: true, sortDirection: 'desc' },
                    ],
                    data: [],
                    param_options: {
                        market: null,
                        expiration: null,
                        search: null,
                        order_by: null,
                        order_ascending: true,
                    },
                    pagination: {
                        current_page: 1,
                        per_page: 10,
                        total: 10,
                    },
                    loaded: false,
                }
            };
        },
        methods: {
            loadTableData() {
                this.table_data.loaded = false;
                axios.get(axios.defaults.baseUrl + '/stats/open-interest/table', {
                    params:{
                        'page': this.table_data.pagination.current_page,
                        "filter_market": this.table_data.param_options.market,
                        "filter_expiration": this.table_data.param_options.expiration ? moment(this.table_data.param_options.expiration).format('YYYY-MM-DD'): null,
                        "search": this.table_data.param_options.search,
                        '_order_by': (this.table_data.param_options.order_by !== null ? this.table_data.param_options.order_by : ''),
                        '_order': (this.table_data.param_options.order_ascending ? 'ASC' : 'DESC'),
                    }
                })
                .then(openInterestsDataResponse => {
                    if(openInterestsDataResponse.status == 200) {
                        // console.log("FROM SERVER: ",openInterestsDataResponse.data);
                        this.table_data.pagination.current_page = openInterestsDataResponse.data.current_page;
                        this.table_data.pagination.per_page = openInterestsDataResponse.data.per_page;
                        this.table_data.pagination.total = openInterestsDataResponse.data.total;
                        this.table_data.data = openInterestsDataResponse.data.data;
                        this.table_data.loaded = true;
                        // console.log(this.table_data.data);
                    } else {
                        console.error(err); 
                    }
                }, err => {
                    console.error(err);
                });
            },
            changePage($event) {
                this.table_data.pagination.current_page = $event;
                this.loadTableData();
            },
            sortingChanged(ctx) {
                this.table_data.param_options.order_by = ctx.sortBy;
                this.table_data.param_options.order_ascending = ctx.sortDesc;
                this.loadTableData();
            },
            formatItem(item, key) {
                if(item[key] == null){
                    return '-';
                }
                switch (key) {
                    case 'open_interest':
                    case 'strike_price':
                    case 'delta':
                    case 'spot_price':
                        return this.$root.splitValHelper(item[key], ' ', 3);
                        break;
                    case 'expiry_date':
                        return moment(item[key], 'YYYY-MM-DD').format('DD MMM YYYY');
                        break;
                    case 'is_put':
                        return item[key] == 1 ? "Put" : "Call";
                        break;
                    default:
                        return item[key];
                }
            },
        },
        mounted() {
        	this.loadTableData();
        }
    }
</script>