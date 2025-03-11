<template>
    <div dusk="safex-table" class="safex-table" >
        <div class="card mt-5">
            <div class="card-header text-center">
                <h2 class="mt-2 mb-2">Rolling 2 Years of Data</h2>
            </div>
            <div class="card-body">
                <b-row>
                    <b-col cols="12">
                        <b-form v-on:submit.prevent="" id="chat-message-form">
                            <b-row class="mt-4">
                                <b-col cols="1" class="d-flex align-items-center">
                                    <label class="mr-sm-2" for="stats-safex-nominal">Min.Nominal:</label>
                                </b-col>
                                <b-col cols="3">
                                    <b-form-select id="stats-safex-table"
                                               class="w-100"
                                               :options="nominal_filter"
                                               v-model="table_data.param_options.nominal">
                                    </b-form-select>
                                </b-col>
                            </b-row>
                            <b-row class="mt-2">
                                <b-col cols="1" class="d-flex align-items-center">
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
                                <b-col cols="1" class="d-flex align-items-center">
                                    <label class="mr-sm-2" for="stats-safex-expiration">Expiration:</label>
                                </b-col>
                                <b-col cols="3">
                                    <b-form-select id="stats-safex-expiration"
                                                class="w-100"
                                                :options="expiration_filter"
                                                v-model="table_data.param_options.expiration">
                                    </b-form-select>
                                </b-col>
                                <b-col cols="2" class="d-flex align-items-center pl-2">
                                    <b-form-checkbox v-model="table_data.param_options.non_expired" 
                                                    class=""
                                                    @change="onExpiredToggled">
                                        Non Expired
                                    </b-form-checkbox>
                                </b-col>
                            </b-row>

                            <b-row class="mt-2">
                                <b-col cols="1" class="d-flex align-items-center">
                                    <label class="mr-sm-2" for="stats-safex-underlying">Underlying:</label>
                                </b-col>
                                <b-col cols="3">
                                    <b-input v-model="table_data.param_options.underlying" class="w-100 mr-0" id="stats-safex-underlying" placeholder="e.g. NPN" />
                                </b-col>
                                <b-col cols="2">
                                    <button type="submit" 
                                            class="btn mm-button w-100 float-right ml-0 mr-2" 
                                            @click="loadTableData()">
                                        Filter
                                    </button>
                                </b-col>
                                <b-col cols="2">
                                    <button class="btn mm-button w-100 float-right ml-0 mr-2" 
                                            @click="clearFilters()">
                                        Clear Filter
                                    </button>
                                </b-col>
                                <b-col cols="4">
                                    <datepicker v-model="table_data.param_options.date"
                                                class="float-right filter-date-picker"
                                                name="safex-table-datepicker"
                                                placeholder="Select a trade date"
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
                    {text: "CTOP", value: "CTOP"},
                    {text: "CTOR", value: "CTOR"},
                    {text: "SINGLES", value: "SINGLES"},
                ],
                expiration_filter: [
                    {text: "All Expirations", value: null},
                ],
                nominal_filter: [
                    {text: "All Nominals", value: null},
                    {text: "R10m to R40m", value: "10-40"},
                    {text: "Greater than R40m", value: ">40"},
                ],
                table_data: {
                    table_fields: [
                        { key: 'trade_date', label: 'Trade Date', tdClass:'text-right', thClass:'text-right', sortable: true, sortDirection: 'desc' },
                        { key: 'structure', label: 'Structure', tdClass:'text-right', thClass:'text-right', sortable: true, sortDirection: 'desc' },
                        { key: 'underlying_alt', label: 'Underlying', tdClass:'text-right', thClass:'text-right', sortable: true, sortDirection: 'desc' },
                        { key: 'strike', label: 'Strike', tdClass:'text-right', thClass:'text-right', sortable: true, sortDirection: 'desc' },
                        { key: 'strike_percentage', label: 'Strike%', tdClass:'text-right', thClass:'text-right', sortable: true, sortDirection: 'desc' },
                        { key: 'is_put', label: 'Put/Call', tdClass:'text-right', thClass:'text-right', sortable: true, sortDirection: 'desc' },
                        { key: 'volspread', label: 'Vol', tdClass:'text-right', thClass:'text-right', sortable: true, sortDirection: 'desc' },
                        { key: 'expiry', label: 'Expiry', tdClass:'text-right', thClass:'text-right', sortable: true, sortDirection: 'desc' },
                        { key: 'nominal', label: 'Nominal', tdClass:'text-right', thClass:'text-right', sortable: true, sortDirection: 'desc' },
                    ],
                    data: [],
                    param_options: {
                        market: null,
                        expiration: null,
                        underlying: null,
                        nominal: null,
                        date: null,
                        order_by: null,
                        order_ascending: false,
                        non_expired: true,
                    },
                    pagination: {
                        current_page: 1,
                        per_page: 10,
                        total: 10,
                    },
                    loaded: false,
                    latest_date: null,
                }
            };
        },
        methods: {
            clearFilters(index) {
                this.table_data.param_options.date = null;
                this.table_data.param_options.market = null;
                this.table_data.param_options.expiration = null;
                this.table_data.param_options.nominal = null;
                this.table_data.param_options.underlying = null;
                this.loadTableData();
            },
            onExpiredToggled(is_toggle = false) {
                if(is_toggle !== null) {
                    this.table_data.param_options.non_expired = !this.table_data.param_options.non_expired;
                }

                const today = new Date();

                if (this.table_data.param_options.non_expired) {
                    this.expiration_filter = this.expiration_filter.filter(date => {
                        if (typeof date === "object" && date.value === null) return true;
                        return new Date(date) > today;
                    });
                    if(this.table_data.param_options.expiration && new Date(this.table_data.param_options.expiration) < today) {
                        this.table_data.param_options.expiration = this.expiration_filter[1]
                    }
                } else {
                    this.expiration_filter = [...this.original_expiration_filter]
                }
            },
            loadTableData() {
                this.table_data.loaded = false;
                axios.get(axios.defaults.baseUrl + '/stats/market-activity/safex', {
                    params:{
                        'page': this.table_data.pagination.current_page,
                        'filter_date': this.table_data.param_options.date ? moment(this.table_data.param_options.date).format('YYYY-MM-DD'): null,
                        "filter_market": this.table_data.param_options.market,
                        "filter_expiration": this.table_data.param_options.expiration ? moment(this.table_data.param_options.expiration).format('YYYY-MM-DD'): null,
                        "filter_nominal": this.table_data.param_options.nominal,
                        "search": this.table_data.param_options.underlying,
                        '_order_by': (this.table_data.param_options.order_by !== null ? this.table_data.param_options.order_by : ''),
                        '_order': (this.table_data.param_options.order_ascending ? 'ASC' : 'DESC'),
                        "filter_non_expired": this.table_data.param_options.non_expired,
                    }
                })
                .then(safexDataResponse => {
                    if(safexDataResponse.status == 200) {
                        this.table_data.pagination.current_page = safexDataResponse.data.data.table_data.current_page;
                        this.table_data.pagination.per_page = safexDataResponse.data.data.table_data.per_page;
                        this.table_data.pagination.total = safexDataResponse.data.data.table_data.total;
                        
                        this.latest_date = safexDataResponse.data.data.latest_date;
                        this.table_data.data = this.resolveFormatData(safexDataResponse.data.data.table_data.data);
                        this.table_data.loaded = true;

                        if(this.expiration_filter.length < 2) {
                            this.expiration_filter = this.expiration_filter.concat(safexDataResponse.data.data.expiration_dates.map(date => moment(date).format('DD MMM YYYY')))
                            this.original_expiration_filter = this.expiration_filter;
                        }

                        this.onExpiredToggled(null)
                    } else {
                        //console.error(err); 
                    }
                }, err => {
                    //console.error(err);
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
                    case 'nominal':
                    case 'strike':
                        return this.$root.splitValHelper(item[key], ' ', 3);
                        break;
                    case 'trade_date':
                    case 'expiry':
                        return moment(item[key], 'YYYY-MM-DD').format('DD MMM YYYY');
                        break;
                    case 'is_put':
                        return item[key] == 1 ? "Put" : "Call";
                        break;
                    default:
                        return item[key];
                }
            },
            isSameDate(date1,date2,test,tet,te) {
                return moment(date1).isSame(date2);
            },
            resolveFormatData(data) {
                data.forEach(row_data => {
                    if(this.isSameDate(row_data.trade_date,this.latest_date)) {
                        row_data._rowVariant = 'latest-data';
                    }
                });
                return data;
            }
        },
        mounted() {
        	this.loadTableData();
        }
    }
</script>