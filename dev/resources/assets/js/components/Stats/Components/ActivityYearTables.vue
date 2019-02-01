<template>
    <div dusk="activity-year-tables" class="activity-year-tables">
        <template v-if="table_years.length > 0">
            <b-card v-bind:class="{ 'mt-5': index == 0 }" :key="index" v-for="(year_data, index) in table_data" no-body class="mb-5">
                <b-card-header header-tag="header" class="p-1" role="tab">
                    <b-btn class="mt-2 mb-2" block href="#" v-b-toggle="accordion_base_id+index" variant="mm-button"><h2>{{ year_data.date }}</h2></b-btn>
                </b-card-header>
                <b-collapse :id="accordion_base_id+index" :visible="active_collapse.index == index" accordion="my-accordion" role="tabpanel">
                    <b-card-body>
                        <b-row>
                            <b-col cols="12">
                                <b-form v-on:submit.prevent="" id="chat-message-form">
                                    <b-row class="mt-4">
                                        <b-col cols="1">
                                            <label class="mr-sm-2" :for="'stats-table-markets'+index">Markets:</label>
                                        </b-col>
                                        <b-col cols="3">
                                            <b-form-select :id="'stats-table-markets'+index"
                                                       class="w-100"
                                                       :options="markets_filter"
                                                       v-model="year_data.filter_market">
                                            </b-form-select>
                                        </b-col>
                                    </b-row>
                                    <b-row class="mt-2">
                                        <b-col cols="1">
                                            <label class="mr-sm-2" :for="'stats-table-expiration'+index">Expiration:</label>
                                        </b-col>
                                        <b-col cols="3">
                                            <b-form-select :id="'stats-table-expiration'+index"
                                                           class="w-100"
                                                           :options="expiration_filter"
                                                           v-model="year_data.filter_expiration">
                                            </b-form-select>
                                        </b-col>
                                    </b-row>
                                    <b-row class="mt-2">
                                        <b-col cols="1">
                                            <label class="mr-sm-2" :for="'stats-table-search'+index">Underlying:</label>
                                        </b-col>
                                        <b-col cols="3">
                                            <b-input v-model="year_data.search" class="w-100 mr-0" :id="'stats-table-search'+index" placeholder="e.g. NPN" />
                                        </b-col>
                                        <b-col cols="2">
                                            <button type="submit" 
                                                    class="btn mm-button w-100 float-right ml-0 mr-2" 
                                                    @click="loadTableData(index, false)">
                                                Filter
                                            </button>
                                        </b-col>
                                        <b-col cols="2">
                                            <button class="btn mm-button w-100 float-right ml-0 mr-2" 
                                                    @click="clearFilters(index)">
                                                Clear Filter
                                            </button>
                                        </b-col>
                                        <b-col cols="4">
                                            <datepicker v-model="year_data.filter_date"
                                                        class="float-right filter-date-picker"
                                                        :name="index+'-table-datepicker'"
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
                        <!-- Main table element -->
                        <b-table v-if="table_data_loaded && year_data.data != null"
                                 class="mt-2 stats-table"
                                 stacked="md"
                                 :items="year_data.data"
                                 :fields="table_fields"
                                 :sort-by.sync="year_data.order_by"
                                 :sort-desc.sync="year_data.order_ascending"
                                 :no-local-sorting="true"
                                 @sort-changed="(e) => sortingChanged(index, e)">
                            <template v-for="(field,key) in table_fields" :slot="field.key" slot-scope="row">
                                {{ formatItem(row.item, field.key) }}
                            </template>
                        </b-table>

                        <b-row v-if="year_data.data != null" class="justify-content-md-center">
                            <b-col md="auto" class="my-1">
                                <b-pagination @change="changePage($event, index)"
                                              :total-rows="year_data.total" 
                                              :per-page="year_data.per_page"
                                              :hide-ellipsis="true"
                                              v-model="year_data.current_page" 
                                              align="center"/>
                            </b-col>
                        </b-row>   
                    </b-card-body>
                </b-collapse>
            </b-card>
        </template>
        <template v-else>
            <div class="card mt-5">
                <div class="card-header text-center">
                    <h2 class="mt-2 mb-2">Yearly Data</h2>
                </div>
                <div class="card-body">
                    <p class="text-center">No Yearly Data to display</p>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        //
    	props: {
            'years': {
                type: Array
            },
            'is_my_activity': {
                type: Boolean
            },
            'is_bank_level': {
                type: Boolean
            },
        },
        data() {
            return {
                accordion_base_id: 'yearTableAccordion',
                table_data_loaded: false,
            	table_years: [],
                active_collapse: {
                    index: 0,
                    state: true,
                },
                table_fields: [
                    { key: 'updated_at', label: 'Date', tdClass:'text-right', thClass:'text-right', sortable: true, sortDirection: 'desc' },
                    { key: 'underlying', label: 'Instrument', tdClass:'text-right', thClass:'text-right' },
                    { key: 'structure', label: 'Structure', tdClass:'text-right', thClass:'text-right', sortable: true, sortDirection: 'desc' },
                    (this.is_my_activity ? { key: 'direction', label: 'Buy/Sell', tdClass:'text-right', thClass:'text-right' } : {}),
                    { key: 'nominal', label: 'Nominal', tdClass:'text-right', thClass:'text-right' },
                    (this.is_my_activity ? {} : { key: 'strike_percentage', label: 'Strike %', tdClass:'text-right', thClass:'text-right' } ),
                    { key: 'strike', label: 'Strike', tdClass:'text-right', thClass:'text-right' },
                    { key: 'volatility', label: 'Level', tdClass:'text-right', thClass:'text-right' },
                    { key: 'expiration', label: 'Expiration', tdClass:'text-right', thClass:'text-right' },
                ],
                table_data:[],
                markets_filter: [
                    {text: "All Markets", value: null},
                ],
                expiration_filter: [
                    {text: "All Expirations", value: null},
                ],
            };
        },
        methods: {
            clearFilters(index) {
                this.table_data[index].filter_date = null;
                this.table_data[index].filter_market = null;
                this.table_data[index].filter_expiration = null;
                this.table_data[index].search = null;
            },
            toggleState(toggle_id) {
                if(toggle_id.indexOf(this.accordion_base_id) !== -1) {
                    let index = toggle_id.substr(toggle_id.indexOf(this.accordion_base_id) + 
                        this.accordion_base_id.length);
                    if(toggle_id == ('accordion'+this.active_collapse.index)) {

                    } else {
                        this.active_collapse.index = index;
                        this.active_collapse.state = true;
                        this.loadTableData(index, true);
                    }
                }
            },
            loadTableData(index, is_toggle) {
                this.table_data_loaded = false;
                if(is_toggle && this.table_data[index].data){
                    this.table_data_loaded = true;
                } else {
                    axios.get(axios.defaults.baseUrl + '/stats/my-activity/year', {
                        params:{
                            'is_bank_level': (this.is_bank_level ? 1 : 0),
                            'is_my_activity': (this.is_my_activity ? 1 : 0),
                            'page': this.table_data[index].current_page,
                            'year': this.table_data[index].date,
                            'filter_date': this.table_data[index].filter_date ? moment(this.table_data[index].filter_date).format('YYYY-MM-DD'): null,
                            "filter_market": this.table_data[index].filter_market,
                            "filter_expiration": this.table_data[index].filter_expiration,
                            "search": this.table_data[index].search,
                            '_order_by': (this.table_data[index].order_by !== null ? this.table_data[index].order_by : ''),
                            '_order': (this.table_data[index].order_ascending ? 'ASC' : 'DESC'),
                        }
                    })
                    .then(activityResponse => {
                        if(activityResponse.status == 200) {
                            this.table_data[index].current_page = activityResponse.data.data.table_data.current_page;
                            this.table_data[index].per_page = activityResponse.data.data.table_data.per_page;
                            this.table_data[index].total = activityResponse.data.data.table_data.total;
                            this.table_data[index].data = activityResponse.data.data.table_data.data;
                            this.table_data_loaded = true;
                            
                            if(this.expiration_filter.length < 2) {
                                this.expiration_filter = this.expiration_filter.concat(activityResponse.data.data.expiration_dates.map(date => moment(date).format('DD MMM YYYY')))
                            }
                        } else {
                            this.table_data_loaded = this.table_data[index].data ? true : false;
                            this.$toasted.error("Failed to load "+index+" year data.")
                            console.error(err); 
                        }
                    }, err => {
                        this.table_data_loaded = this.table_data[index].data ? true : false;
                        this.$toasted.error("Failed to load "+index+" year data.")
                        console.error(err);
                    });
                }
            },
            loadMarkets() {
                axios.get(axios.defaults.baseUrl + '/stats/my-activity/markets')
                .then(marketsResponse => {
                    if(marketsResponse.status == 200) {
                        Object.keys(marketsResponse.data).forEach(key => {
                            this.markets_filter.push({
                                text: marketsResponse.data[key],
                                value: key
                            });
                        });
                    } else {
                        console.error(err); 
                    }
                }, err => {
                    console.error(err);
                });
            },
            initTableData() {
                this.table_years.forEach( (element, key) => {
                    this.table_data.push({
                        data: null,
                        date: element,
                        current_page: 1,
                        per_page: 10,
                        total: 10,
                        filter_date: null,
                        filter_market: null,
                        filter_expiration:null,
                        search: null,
                        order_by: null,
                        order_ascending: false
                    });
                });
                this.active_collapse.index = 0;
                this.loadTableData(0, false)
            },
            sortingChanged(index, ctx) {
                this.table_data[index].order_by = ctx.sortBy;
                this.table_data[index].order_ascending = ctx.sortDesc;
                this.loadTableData(index, false);
            },
            formatItem(item, key) {
                if(item[key] == null){
                    return '-';
                }
                if( Array.isArray(item[key]) ) {
                    return this.formatArrayItem(item[key], key); 
                }
                if( Array.isArray(item[key]) ) {
                    let formatted = '';
                    item[key].forEach(element => {
                        formatted += (key == 'expiration' ? this.castToMoment(element) : element) + ' / ';
                    });
                    return formatted.substring(0, formatted.length - 3);
                }

                switch (key) {
                    case 'updated_at':
                        return this.castToMoment(item[key]);
                        break;
                    default:
                        return item[key];
                }
            },
            formatArrayItem(array_item, key) {
                if(array_item.length < 1) {
                    return '-';
                }
                let formatted_element = '';
                switch (key) {
                    case 'expiration':
                        formatted_element = array_item.map(x => this.castToMoment(x)).join(' / ');
                        break;
                    case 'strike':
                    case 'nominal':
                        formatted_element = array_item.map(x => x[0] == 'R' ? x : this.$root.splitValHelper(x, ' ', 3)).join(' / ');
                        break;
                    case 'underlying':
                        formatted_element = array_item.join(' vs. ');
                        break;
                    default:
                        formatted_element = array_item.join(' / ');
                }

                return formatted_element;
            },
            /**
             * Casting a passed string to moment with a new format
             *
             * @param {string} date_string
             */
            castToMoment(date_string) {
                return moment(date_string, 'YYYY-MM-DD HH:mm:ss').format('DD MMM YYYY');
            },
            changePage($event, index) {
                this.table_data[index].current_page = $event;
                this.loadTableData(index, false);
            },
            setTableFields() {
                if(this.is_bank_level) {
                    this.table_fields.push({ key: 'seller', label: 'Seller' });
                    this.table_fields.push({ key: 'buyer', label: 'Buyer' });
                } else {
                    this.table_fields.push(
                        { key: 'status', label: 'Status' },
                    );
                    if(this.is_my_activity) {
                        this.table_fields.push(
                            { key: 'trader', label: 'Trader' },
                        );
                    }
                }
            }
        },
        mounted() {
            this.table_years = [];
            if(this.years.length > 0) {
                let unordered_years = [];
                this.setTableFields();
                this.years.forEach(element => {
                    unordered_years.push(element.year);
                });
                this.$root.dateStringArraySort(unordered_years, 'YYYY');
                this.table_years = unordered_years.reverse();
                this.$root.$on('bv::toggle::collapse',this.toggleState);
                this.loadMarkets();
                this.initTableData();
            }
        }
    }
</script>