<template>
    <div dusk="rebates-year-tables" class="rebates-year-tables">
        <b-card v-bind:class="{ 'mt-5': index == 0 }" :key="index" v-for="(year, index) in table_years" no-body class="mb-5">
            <b-card-header header-tag="header" class="p-1" role="tab">
                <b-btn class="mt-2 mb-2" block href="#" v-b-toggle="accordion_base_id+index" variant="mm-button"><h2>{{ year }}</h2></b-btn>
            </b-card-header>
            <b-collapse :id="accordion_base_id+index" :visible="active_collapse.index == index" accordion="my-accordion" role="tabpanel">
                <b-card-body>
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
                accordion_base_id: 'yearTableAccordion',
                table_data_loaded: false,
            	table_years: [],
                active_collapse: {
                    index: 0,
                    state: true,
                },
                table_fields: [
                    { key: 'date', label: 'Date'/*, sortable: true, sortDirection: 'desc'*/ },
                    { key: 'market', label: 'Instrument' },
                    { key: 'is_put', label: 'Option Strategy' },
                    { key: 'strike', label: 'Strike' },
                    { key: 'expiration', label: 'Expiration' },
                    { key: 'nominal', label: 'Nominal (ZAR)' },
                    { key: 'role', label: 'Your Role' },
                    { key: 'rebate', label: 'Rebate' },
                ],
                table_data:{},
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
                    axios.get(axios.defaults.baseUrl + '/rebates-summary/year', {
                        params:{
                            'page': this.table_data[index].current_page,
                            'year': this.table_years[index],
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
            initTableData() {
                if(this.table_years.length > 0) {
                    this.table_years.forEach( (element, key) => {
                        this.table_data[key] = {
                            data: null,
                            date: element,
                            current_page: 1,
                            per_page: 10,
                            total: 10,
                            order_by: null,
                            order_ascending: true
                        };
                    });
                    this.loadTableData(0, false);
                }
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

                switch (key) {
                    case 'date':
                        return this.castToMoment(item[key])
                        break;
                    case 'rebate':
                        return this.$root.splitValHelper(item[key], ' ', 3);
                        break;
                    case 'is_put':
                        return item[key] == 1 ? "Put" : "Call";
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