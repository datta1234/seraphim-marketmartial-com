<template>
    <div dusk="my-activity-year-tables" class="my-activity-year-tables">
        <b-card v-bind:class="{ 'mt-5': index == 0 }" :key="index" v-for="(year, index) in table_years" no-body class="mb-5">
            <b-card-header header-tag="header" class="p-1" role="tab">
                <b-btn block href="#" v-b-toggle="'accordion'+index" variant="info">{{ year }}</b-btn>
            </b-card-header>
            <b-collapse :id="'accordion'+index" :visible="active_collapse.index == index" accordion="my-accordion" role="tabpanel">
                <b-card-body>
                    <datepicker @selected="applyDateFilter(index)" 
                                v-model="table_data[index].filter_date" 
                                :name="year+'-table-datepicker'">
                                <span slot="afterDateInput" class="animated-placeholder">
                                    Choose a Date
                                </span>                        
                    </datepicker>
                    <!-- Main table element -->
                    <b-table v-if="table_data_loaded && table_data[index].data != null"
                             class="mt-2 stats-table"
                             stacked="md"
                             :items="table_data[index].data"
                             :fields="table_fields"
                             :sort-by.sync="sort_options.order_by"
                             :sort-desc.sync="sort_options.order_ascending"
                             :no-local-sorting="true"
                             @sort-changed="sortingChanged">
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
    </div>
</template>

<script>
    export default {
        //
    	props: {
            'years': {
                type: Array
            },
        },
        data() {
            return {
                table_data_loaded: false,
            	table_years: [],
                active_collapse: {
                    index: 0,
                    state: true,
                },
                table_fields: [
                    { key: 'updated_at', label: 'Date' },
                    { key: 'market', label: 'Instrument' },
                    { key: 'structure', label: 'Structure' },
                    { key: 'direction', label: 'Direction' },
                    { key: 'nominal', label: 'Nominal' },
                    { key: 'strike_percentage', label: 'Strike %' },
                    { key: 'strike', label: 'Strike' },
                    { key: 'volatility', label: 'Volatility' },
                    { key: 'expiration', label: 'Expiration' },
                    { key: 'status', label: 'Status' },
                    { key: 'trader', label: 'Trader' },
                ],
                table_data:{},
                    sort_options: {
                    search: '',
                    filter: null,
                    order_by: null,
                    order_ascending: true,
                },
            };
        },
        methods: {
            toggleState(toggle_id) {
                let index = toggle_id.substr(toggle_id.indexOf('accordion') + 9);
                if(toggle_id == ('accordion'+this.active_collapse.index)) {

                } else {
                    this.active_collapse.index = index;
                    this.active_collapse.state = true;
                    this.loadTableData(index, true);
                }
            },
            loadTableData(index, is_toggle) {
                this.table_data_loaded = false;
                console.log("Loading table data",this.table_data);
                if(is_toggle && this.table_data[index].data){
                    this.table_data_loaded = true;
                } else {
                    axios.get(axios.defaults.baseUrl + 'my-activity/year', {
                        params:{
                            'page': this.table_data[index].current_page,
                            'year': this.table_years[index],
                            'filter_date': this.table_data[index].filter_date ? moment(this.table_data[index].filter_date).format('YYYY-MM-DD'): null,
                        }
                    })
                    .then(activityResponse => {
                        if(activityResponse.status == 200) {
                            console.log("FROM SERVER: ",activityResponse.data);
                            this.table_data[index].current_page = activityResponse.data.current_page;
                            this.table_data[index].per_page = activityResponse.data.per_page;
                            this.table_data[index].total = activityResponse.data.total;
                            this.table_data[index].data = activityResponse.data.data;
                            this.table_data[index].filter_date = null;
                            this.table_data_loaded = true;
                            console.log("Data: ",this.table_data[index].data);
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
            initTableData() {
                this.table_years.forEach( (element, key) => {
                    this.table_data[key] = {
                        data: null,
                        date: element,
                        filter_date: null,
                        current_page: 1,
                        per_page: 10,
                        total: 10,
                    };
                });
                this.loadTableData(0, false)
            },
            sortingChanged(ctx) {
                console.log("I CHANGE!!!", ctx);
            },
            formatItem(item, key) {
                if(item[key] == null){
                    return '-';
                }
                if( Array.isArray(item[key]) ) {
                    let formatted = '';
                    item[key].forEach(element => {
                        formatted += (key == 'expiration' ? this.castToMoment(element) : element) + ' / ';
                    });
                    return formatted.substring(0, formatted.length - 3);
                }

                return key == 'updated_at' ? this.castToMoment(item[key]) : item[key];
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
            applyDateFilter(index) {
                Vue.nextTick( () => {
                    this.loadTableData(index, false);
                });
            },
        },
        mounted() {
            let unordered_years = [];
            this.years.forEach(element => {
                unordered_years.push(element.year);
            });
            this.$root.dateStringArraySort(unordered_years, 'YYYY');
            this.table_years = unordered_years.reverse();
            this.$root.$on('bv::toggle::collapse',this.toggleState);
            this.initTableData();
        }
    }
</script>