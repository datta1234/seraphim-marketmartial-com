<template>
    <div dusk="safex-table" class="safex-table" >
        <div class="card mt-5">
            <div class="card-header text-center">
                <h2 class="mt-2 mb-2">Rolling 6 Months of Safex Data</h2>
            </div>
            <div class="card-body">
                <b-row>
                    <b-col cols="12">
                        <b-form v-on:submit.prevent="" id="chat-message-form">
                            <b-row class="mt-4">
                                <b-col cols="1">
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
                                    <label class="mr-sm-2" for="stats-safex-expiration">Expiration:</label>
                                </b-col>
                                <b-col cols="3">
                                    <b-form-select id="stats-safex-expiration"
                                                   class="w-100"
                                                   :options="expiration_filter"
                                                   v-model="table_data.param_options.expiration">
                                    </b-form-select>
                                </b-col>
                            </b-row>
                            <b-row class="mt-2">
                                <b-col cols="1">
                                    <label class="mr-sm-2" for="stats-safex-search">Underlying:</label>
                                </b-col>
                                <b-col cols="3">
                                    <b-input v-model="table_data.param_options.search" class="w-100 mr-0" id="stats-safex-search" placeholder="e.g. NPN" />
                                </b-col>
                                <b-col cols="2">
                                    <button type="submit" 
                                            class="btn mm-button w-100 float-right ml-0 mr-2" 
                                            @click="loadTableData()">
                                        Filter
                                    </button>
                                </b-col>
                                <b-col cols="4" offset="2">
                                    <datepicker v-model="table_data.param_options.date"
                                                class="float-right filter-date-picker"
                                                name="safex-table-datepicker"
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
                ],
                expiration_filter: [
                    {text: "All Expirations", value: null},
                ],
                nominal_filter: [
                    {text: "All Nominals", value: null},
                ],
                
                
                table_data: {
                    table_fields: [
                        { key: 'trade_date', label: 'Trade Date' },
                        { key: 'structure', label: 'Strucure' },
                        { key: 'underlying_alt', label: 'Underlying' },
                        { key: 'strike', label: 'Strike' },
                        { key: 'strike_percentage', label: 'Strike%' },
                        { key: 'is_put', label: 'Put/Call' },
                        { key: 'volspread', label: 'Vol' },
                        { key: 'expiry', label: 'Expiry' },
                        { key: 'nominal', label: 'Nominal' },
                    ],
                    data: null,
                    param_options: {
                        market: null,
                        expiration: null,
                        search: null,
                        nominal: null,
                        date: null,
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
                axios.get(axios.defaults.baseUrl + '/stats/market-activity/safex'/*, {
                    params:{
                        'page': this.table_data[index].current_page,
                        'year': this.table_years[index],
                        'filter_date': this.table_data[index].filter_date ? moment(this.table_data[index].filter_date).format('YYYY-MM-DD'): null,
                        "filter_market": this.table_data[index].filter_market,
                        "filter_expiration": this.table_data[index].filter_expiration,
                        "search": this.table_data[index].search,
                        '_order_by': (this.table_data[index].order_by !== null ? this.table_data[index].order_by : ''),
                        '_order': (this.table_data[index].order_ascending ? 'ASC' : 'DESC'),
                    }
                }*/)
                .then(safexDataResponse => {
                    if(safexDataResponse.status == 200) {
                        console.log("FROM SERVER: ",safexDataResponse.data);
                        this.table_data.pagination.current_page = safexDataResponse.data.current_page;
                        this.table_data.pagination.per_page = safexDataResponse.data.per_page;
                        this.table_data.pagination.total = safexDataResponse.data.total;
                        this.table_data.data = safexDataResponse.data.data;
                        this.table_data.loaded = true;
                    } else {
                        console.error(err); 
                    }
                }, err => {
                    console.error(err);
                });
            },
            changePage($event) {
                this.pagination.current_page = $event;
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
                    default:
                        return item[key];
                }
            },
        },
        mounted() {
        	this.loadTableData();
            this.table_data.loaded = true;
        }
    }
</script>