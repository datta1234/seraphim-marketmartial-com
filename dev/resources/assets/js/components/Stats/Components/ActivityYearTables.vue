<template>
    <div dusk="activity-year-tables" class="activity-year-tables">
        <template v-if="table_years.length > 0">
            <b-card v-bind:class="{ 'mt-5': index == 0 }" :key="index" v-for="(year, index) in table_years" no-body class="mb-5">
                <b-card-header header-tag="header" class="p-1" role="tab">
                    <b-btn class="mt-2 mb-2" block href="#" v-b-toggle="accordion_base_id+index" variant="mm-button"><h2>{{ year }}</h2></b-btn>
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
                                                       v-model="table_data[index].filter_market">
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
                                                           v-model="table_data[index].filter_expiration">
                                            </b-form-select>
                                        </b-col>
                                    </b-row>
                                    <b-row class="mt-2">
                                        <b-col cols="1">
                                            <label class="mr-sm-2" :for="'stats-table-search'+index">Underlying:</label>
                                        </b-col>
                                        <b-col cols="3">
                                            <b-input v-model="table_data[index].search" class="w-100 mr-0" :id="'stats-table-search'+index" placeholder="e.g. NPN" />
                                        </b-col>
                                        <b-col cols="2">
                                            <button type="submit" 
                                                    class="btn mm-button w-100 float-right ml-0 mr-2" 
                                                    @click="loadTableData(index, false)">
                                                Filter
                                            </button>
                                        </b-col>
                                        <b-col cols="4" offset="2">
                                            <datepicker v-model="table_data[index].filter_date"
                                                        class="float-right filter-date-picker"
                                                        :name="year+'-table-datepicker'"
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
                        <b-table v-if="table_data_loaded && table_data[index].data != null"
                                 class="mt-2 stats-table"
                                 stacked="md"
                                 :items="table_data[index].data"
                                 :fields="table_fields"
                                 :sort-by.sync="table_data[index].order_by"
                                 :sort-desc.sync="table_data[index].order_ascending"
                                 :no-local-sorting="true"
                                 @sort-changed="(e) => sortingChanged(index, e)">
                            <template v-for="(field,key) in table_fields" :slot="field.key" slot-scope="row">
                                {{ formatItem(row.item, field.key) }}
                            </template>
                        </b-table>

                        <b-row v-if="table_data[index].data != null" class="justify-content-md-center">
                            <b-col md="auto" class="my-1">
                                <b-pagination @change="changePage($event, index)"
                                              :total-rows="table_data[index].total" 
                                              :per-page="table_data[index].per_page"
                                              :hide-ellipsis="true"
                                              v-model="table_data[index].current_page" 
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
                    { key: 'updated_at', label: 'Date'/*, sortable: true, sortDirection: 'desc'*/ },
                    { key: 'market', label: 'Instrument'/*, sortable: true, sortDirection: 'desc'*/ },
                    { key: 'structure', label: 'Structure'/*, sortable: true, sortDirection: 'desc'*/ },
                    (this.is_my_activity ? { key: 'direction', label: 'Direction'/*, sortable: true, sortDirection: 'desc'*/ } : {}),
                    { key: 'nominal', label: 'Nominal' },
                    { key: 'strike_percentage', label: 'Strike %' },
                    { key: 'strike', label: 'Strike' },
                    { key: 'volatility', label: 'Volatility' },
                    { key: 'expiration', label: 'Expiration' },
                ],
                table_data:{},
                markets_filter: [
                    {text: "All Markets", value: null},
                ],
                expiration_filter: [
                    {text: "All Expirations", value: null},
                ],
            };
        },
        methods: {
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
                            'year': this.table_years[index],
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
                            this.table_data[index].current_page = activityResponse.data.current_page;
                            this.table_data[index].per_page = activityResponse.data.per_page;
                            this.table_data[index].total = activityResponse.data.total;
                            this.table_data[index].data = activityResponse.data.data;
                            this.table_data_loaded = true;
                        } else {
                            this.table_data_loaded = this.table_data[index].data ? true : false;
                            this.$toasted.error("Failed to load "+this.table_years[index]+" year data.")
                            console.error(err); 
                        }
                    }, err => {
                        this.table_data_loaded = this.table_data[index].data ? true : false;
                        this.$toasted.error("Failed to load "+this.table_years[index]+" year data.")
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
            loadExpirations() {
                axios.get(axios.defaults.baseUrl + '/trade/safex-expiration-date', {
                    params:{
                        'not_paginate': true,
                    }
                })
                .then(expirationsResponse => {
                    Object.keys(expirationsResponse.data.data).forEach(key => {
                        this.expiration_filter.push(moment(expirationsResponse.data.data[key].date).format('DD MMM YYYY'));
                    });
                }, err => {
                    console.error(err);
                    this.$toasted.error("Failed to load safex expiration dates");
                });
            },
            initTableData() {
                this.table_years.forEach( (element, key) => {
                    this.table_data[key] = {
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
                        order_ascending: true
                    };
                });
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
                let formatted_array = '';
                array_item.forEach(element => {
                    switch (key) {
                        case 'expiration':
                            formatted_array += this.castToMoment(element) + ' / ';
                            break;
                        case 'strike':
                        case 'nominal':
                            formatted_array += this.$root.splitValHelper(element, ' ', 3) + ' / ';
                            break;
                        case 'market':
                            formatted_array += element + ' vs. ';
                            break;
                        default:
                            formatted_array += element + ' / ';
                    }
                });
                return formatted_array.substring(0, formatted_array.length - 3);
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
                this.loadExpirations();
                this.initTableData();
            }
        }
    }
</script>