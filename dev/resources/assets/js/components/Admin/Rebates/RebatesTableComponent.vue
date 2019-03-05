<template>
    <div dusk="rebates-table" class="rebates-table" >
    	<b-form v-on:submit.prevent="" id="rebates-filter-form">
            <b-row class="mt-2">
                <b-col cols="1">
                    <label class="mr-sm-2" for="admin-filter-paid">Filter Status:</label>
                </b-col>
                <b-col cols="2">
                    <b-form-select id="admin-filter-paid"
                                   class="w-100"
                                   :options="filter_options"
                                   v-model="sort_options.filter_paid">
                    </b-form-select>
                </b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col cols="1">
                    <label class="mr-sm-2" for="rebates-table-search">Search:</label>
                </b-col>
                <b-col cols="2">
                    <b-input v-model="sort_options.search" class="w-100 mr-0" id="rebates-table-search" placeholder="Search Term" />
                </b-col>
                <b-col cols="2">
                    <button type="submit" 
                            class="btn mm-button w-75 ml-0 mr-2" 
                            @click="loadRebates()">
                        Filter
                    </button>
                </b-col>
                <b-col cols="4" offset="3">
                    <datepicker v-model="sort_options.date_filter"
                                class="float-right filter-date-picker"
                                name="'rebate-datepicker'"
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
        <!-- Main table element -->
        <b-table v-if="rebates_loaded && items != null"
                 class="mt-2 admin-users-table"
                 stacked="md"
                 :items="items"
                 :fields="table_fields"
                 :sort-by.sync="sort_options.order_by"
                 :sort-desc.sync="sort_options.order_ascending"
                 :no-local-sorting="true"
                 @sort-changed="sortingChanged">
            <template v-for="(field,key) in table_fields" :slot="field.key" slot-scope="row">
                {{ formatItem(row.item, field.key) }}
            </template>
            <template slot="action" slot-scope="data">
                <b-row>
                    <b-col cols="6" class="pr-1">
                        <button v-if="data.item.is_paid" 
                                type="button" 
                                class="btn mm-generic-trade-button w-100"
                                @click="showModal(data.item, {'is_paid': false}, data.index, 'Mark this Rebate as Not Paid')">
                            Unmark Paid
                        </button>
                        <button v-else 
                                type="button" 
                                class="btn mm-generic-trade-button w-100"
                                @click="showModal(data.item, {'is_paid': true}, data.index, 'Mark this Rebate as Paid')">
                            Mark as Paid
                        </button>
                    </b-col>
                    <b-col cols="6" class="pl-1">
                        <edit-rebate :item_data="data.item" 
                                     :item_index="data.index" 
                                     :callback="updateRebateAmount">
                        </edit-rebate>
                    </b-col>
                </b-row>
            </template>
        </b-table>

        <b-row v-if="items != null" class="justify-content-md-center">
          <b-col md="auto" class="my-1">
            <b-pagination @change="changePage($event)"
                          :total-rows="total" 
                          :per-page="per_page"
                          :hide-ellipsis="true"
                          v-model="current_page" 
                          align="center"/>
          </b-col>
        </b-row>

        <!-- Confirmations Modal -->
        <b-modal class="mm-modal mx-auto" v-model="modal_data.show_modal" :ref="modal_data.modal_ref">
            <!-- Modal title content --> 
            <div class="mm-modal-title" slot="modal-title">
                Confirmation
            </div>

            <b-row class="justify-content-md-center">
                <b-col class="text-center" cols="12">
                    <p class="modal-info-text">Are you sure you want to {{ modal_data.confirm_message }}?</p>
                </b-col>
            </b-row>

            <!-- Modal footer content -->
            <div slot="modal-footer" class="w-100">
                <b-row align-v="center">
                    <b-col cols="12">
                        <b-button class="mm-modal-button ml-2 w-25" @click="rebateAction()">Ok</b-button>
                        <b-button class="mm-modal-button ml-2 w-25" @click="hideModal()">Cancel</b-button>
                    </b-col>
                </b-row>
           </div>
        </b-modal>
        <!-- END Confirmations Modal -->
    </div>
</template>

<script>
    // Components
    import EditRebate from './EditRebateComponent.vue';
    export default {
        components: {
            EditRebate,
        },
    	props: [
            'rebate_data',
        ],
        data() {
            return {
            	items:  null,
                table_fields: [
                    { key: 'date', label: 'Date' },
                    { key: 'user', label: 'Username' },
                    { key: 'organisation', label: 'Organisation' },
                    { key: 'market', label: 'Instrument' },
                    { key: 'is_put', label: 'Put/Call' },
                    { key: 'strike', label: 'Strike' },
                    { key: 'expiration', label: 'Expiration' },
                    { key: 'nominal', label: 'Nominal' },
                    { key: 'rebate', label: 'Rebate' },
                    { key: 'is_paid', label: 'Paid' },
                    { key: 'action', label: 'Action' },
                ],
                current_page: 1,
                per_page: 10,
                total: 10,
                path: '',
                rebates_loaded: true,
                initial_load: 0,
                filter_options: [
                    {text: "All", value: null},
                    {text: "Paid", value: '1'},
                    {text: "Not Paid", value: '0'},
                ],
                sort_options: {
                    date_filter: null,
                    search: '',
                    filter_paid: null,
                    order_by: null,
                    order_ascending: true,
                },
                modal_data: {
                    rebate_action: null,
                    rebate: null,
                    rebate_index: null,
                    confirm_message: '',
                    show_modal: false,
                    modal_ref: 'confirm-action-modal',
                },
            };
        },
        methods: {
            changePage($event) {
                this.current_page = $event;
                this.loadRebates();    
            },
            searchTerm() {
                this.current_page = 1;
                this.loadRebates();
            },
            loadRebates() {
                axios.get(this.path, {
                    params:{
                        'page': this.current_page,
                        'search': this.sort_options.search,
                        '_order_by': (this.sort_options.order_by !== null ? this.sort_options.order_by : ''),
                        '_order': (this.sort_options.order_ascending ? 'ASC' : 'DESC'),
                        'filter_paid': (this.sort_options.filter_paid !== null ? this.sort_options.filter_paid : ''),
                        'date_filter': (this.sort_options.date_filter !== null ? moment(this.sort_options.date_filter).format('YYYY-MM-DD'): null),
                    }
                })
                .then(rebatesResponse => {
                    if(rebatesResponse.status == 200) {
                        this.current_page = rebatesResponse.data.current_page;
                        this.per_page = rebatesResponse.data.per_page;
                        this.total = rebatesResponse.data.total;
                        this.items = rebatesResponse.data.data;
                        /*EventBus.$emit('loading', 'requestDates');
                        this.dates_loaded = true;*/
                        this.rebates_loaded = true;
                    } else {
                        //console.error(err);    
                    }
                }, err => {
                    //console.error(err);
                });
            },
            userStatus(user) {
                if(user.verified) {
                    if(user.active) {
                        return "Active";
                    }
                    return "Inactive";
                }
                return "Request";
            },
            rebateAction() {
                let index = this.modal_data.rebate_index;
                axios.put(axios.defaults.baseUrl + '/admin/rebates/'+this.modal_data.rebate.id, this.modal_data.rebate_action)
                .then(rebatesResponse => {
                    if(rebatesResponse.status == 200) {
                        this.items[index].is_paid = rebatesResponse.data.data.is_paid;
                        this.hideModal();
                        this.$toasted.success(rebatesResponse.data.message);
                    } else {
                        this.$toasted.error(rebatesResponse.data.message);
                    }
                }, err => {
                    //console.error(err);
                });
            },
            updateRebateAmount(new_amount, index) {
                this.items[index].rebate = new_amount;
            },
            /**
             * Loads the Confirmation Modal 
             */
            showModal(rebate, action, index, message) {
                this.modal_data.confirm_message = message;
                this.modal_data.rebate = rebate;
                this.modal_data.rebate_action = action;
                this.modal_data.rebate_index = index;
                this.modal_data.show_modal = true;
                this.$refs[this.modal_data.modal_ref].$on('hidden', this.hideModal);
            },
            /**
             * Closes the Confirmation Modal 
             */
            hideModal() {
                this.modal_data.rebate = null;
                this.modal_data.rebate_action = null;
                this.modal_data.rebate_index = null;
                this.modal_data.confirm_message = '';
                this.modal_data.show_modal = false;
                this.$refs[this.modal_data.modal_ref].$off('hidden', this.hideModal);
            },
            sortingChanged(ctx) {
                this.sort_options.order_by = ctx.sortBy;
                this.sort_options.order_ascending = ctx.sortDesc;
                this.loadRebates();
            },
            filterChanged(value) {
                this.sort_options.filter = value;
                this.loadRebates();
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
                        return this.castToMoment(item[key].date);
                        break;
                    case 'rebate':
                        return this.$root.splitValHelper(item[key], ' ', 3);
                        break;
                    case 'is_put':
                        return item[key] == 1 ? "Put" : "Call";
                        break;
                    case 'is_paid':
                        return item[key] == 1 ? "Yes" : "No";
                        break;
                    default:
                        return item[key];
                }
            },
            formatArrayItem(array_item, key) {
                if(array_item.length < 1) {
                    return '-';
                }
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
                return formatted_array.substring(0, formatted_array.length - (key == 'market' ? 5 : 3));
            },
            /**
             * Casting a passed string to moment with a new format
             *
             * @param {string} date_string
             */
            castToMoment(date_string) {
                return moment(date_string, 'YYYY-MM-DD HH:mm:ss').format('DD MMM YYYY');
            },
        },
        mounted() {
        	let parsed_data = JSON.parse(this.rebate_data);
            this.items = parsed_data.data;
            this.current_page = parsed_data.current_page;
            this.per_page = parsed_data.per_page;
            this.total = parsed_data.total;
            this.path = parsed_data.path;
        }
    }
</script>