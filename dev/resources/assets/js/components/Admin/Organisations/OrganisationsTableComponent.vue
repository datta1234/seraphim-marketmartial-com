<template>
    <div dusk="organisations-table" class="organisations-table">
        <b-form v-on:submit.prevent="" id="chat-message-form">
            <b-row>
                <b-col cols="8">
                    <label class="mr-sm-2" for="admin-filter-organisations">Filter by Status:</label>
                    <b-form-select id="admin-filter-organisations"
                                   class="w-25"
                                   :options="filter_options"
                                   v-model="sort_options.filter"
                                   @change="filterChanged">
                    </b-form-select>
                </b-col>
                <b-col cols="4">
                    <slot></slot>
                    <button type="submit" 
                            class="btn mm-generic-trade-button float-right ml-0 mr-2" 
                            @click="searchTerm">
                        <i class="fas fa-search"></i>
                    </button>
                    <b-input v-model="sort_options.search" class="w-50 float-right mr-0" id="admin-Organisations-search" placeholder="Search" />
                </b-col>
            </b-row>
        </b-form>
        <!-- Main table element -->
        <b-table v-if="organisations_loaded && items != null "
                 class="mt-2 admin-organisations-table"
                 stacked="md"
                 :items="items"
                 :fields="fields"
                 :sort-by.sync="sort_options.order_by"
                 :sort-desc.sync="sort_options.order_ascending"
                 :no-local-sorting="true"
                 @sort-changed="sortingChanged">  
            <template slot="organisation_title" slot-scope="row">
                {{ row.item.organisation.title }}
            </template>
            <template slot="status" slot-scope="row">
                {{ OrganisationStatus(row.item) }}
            </template>
            <template slot="toggle_chat" slot-scope="data">
                <!-- Rounded toggle switch -->
                <div class="float-right">
                    <span class="toggle"></span>
                    <label class="switch mb-0 ml-1" :id="'theme-toggle-'+data.index">
                        <input type="checkbox" 
                               @click="toggleChat($event, data.item, data.index)" 
                               v-model="data.item.slack_text_chat">
                        <span class="slider round"></span>
                    </label>
                </div>
            </template>
        </b-table>
        <b-row v-if="items != null && items.length == 0" class="justify-content-md-center">
          <b-col md="auto" class="my-1">
            <p class="text-center">No results found.</p>
          </b-col>
        </b-row>
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

            <b-row v-if="modal_data.organisation" class="justify-content-md-center">
                <b-col class="text-center" cols="12">
                    <p class="modal-info-text">Are you sure you want to apply the following on {{ modal_data.organisation.title }}? <br>
                        {{ modal_data.confirm_actions }}
                    </p>
                </b-col>
            </b-row>
            <b-row v-if="modal_data.organisation && modal_data.organisation_action.verified && modal_data.organisation.organisation.verified == 1" class="justify-content-md-center">
                <b-col class="text-center" cols="12">
                    <p class="modal-info-text">Note Verifying this organisation will verify the following organisation as well:<br>{{modal_data.organisation.organisation.title}}</p>
                </b-col>
            </b-row>

            <!-- Modal footer content -->
            <div slot="modal-footer" class="w-100">
                <b-row align-v="center">
                    <b-col cols="12">
                        <b-button class="mm-modal-button ml-2 w-25" @click="organisationAction()">Ok</b-button>
                        <b-button class="mm-modal-button ml-2 w-25" @click="hideModal()">Cancel</b-button>
                    </b-col>
                </b-row>
           </div>
        </b-modal>
        <!-- END Confirmations Modal -->
    </div>
</template>

<script>
export default {
    props: [
            'organisation_data',
        ],
    computed: {
        base_url() {
            return axios.defaults.baseUrl;
        }
    },
    data () {
        return {
            items:  null,
            fields: [
                { key: 'title', label: 'Organisation', sortable: true, sortDirection: 'desc' },
                { key: 'status', label: 'Status', sortable: true, sortDirection: 'desc' },
                { key: 'slack_text_chat', label: 'Chat Status', sortable: true, sortDirection: 'desc' },
                { key: 'toggle_chat', label: 'Toggle Chat' },
            ],
            current_page: 1,
            per_page: 10,
            total: 10,
            path: '',
            organisations_loaded: true,
            initial_load: 0,
            action_selected: 'select',
            filter_options: [
                {text: "All", value: null},
                {text: "Verified", value: 'active'},
                {text: "Unverified", value: 'inactive'},
                {text: "Chat Activated", value: 'chat_full'},
                {text: "Chat Deactivated", value: 'chat_limited'},
            ],
            sort_options: {
                search: '',
                filter: null,
                order_by: null,
                order_ascending: true,
            },
            modal_data: {
                organisation_action: null,
                organisation: null,
                organisation_index: null,
                confirm_actions: '',
                show_modal: false,
                modal_ref: 'confirm-action-modal',
            }       
        }
    },
    methods: {
        toggleChat($event, organisation, index) {
            $event.preventDefault();
            this.showModal(
                organisation,
                {'slack_text_chat': true},
                index, 
                "- "+(organisation.slack_text_chat ? "Activate" : 'Deactivate') + " organisation chat.")
        },
        changePage($event) {
            this.current_page = $event;
            this.loadOrganisations();    
        },
        searchTerm() {
            this.current_page = 1;
            this.loadOrganisations();
        },
        loadOrganisations() {
            axios.get(this.path, {
                params:{
                    'page': this.current_page,
                    'search': this.sort_options.search,
                    '_order_by': (this.sort_options.order_by !== null ? this.sort_options.order_by : ''),
                    '_order': (this.sort_options.order_ascending ? 'ASC' : 'DESC'),
                    'filter': (this.sort_options.filter !== null ? this.sort_options.filter : ''),
                }
            })
            .then(OrganisationsResponse => {
                if(OrganisationsResponse.status == 200) {
                    this.current_page = OrganisationsResponse.data.current_page;
                    this.per_page = OrganisationsResponse.data.per_page;
                    this.total = OrganisationsResponse.data.total;
                    this.items = OrganisationsResponse.data.data;
                    /*EventBus.$emit('loading', 'requestDates');
                    this.dates_loaded = true;*/
                    this.organisations_loaded = true;
                } else {
                    //console.error(err);    
                }
            }, err => {
                //console.error(err);
            });
        },
        OrganisationStatus(organisation) {
            if(organisation.verified) {
                return "Verified";
            }
            return "Unverified";
        },
        organisationAction() {
            let index = this.modal_data.organisation_index;
            axios.put(axios.defaults.baseUrl + '/admin/organisation-man/'+this.modal_data.organisation.id, this.modal_data.organisation_action)
            .then(OrganisationsResponse => {
                if(OrganisationsResponse.status == 200) {
                    this.items[index].slack_text_chat = OrganisationsResponse.data.data.slack_text_chat;
                    this.hideModal();
                    this.$toasted.success(OrganisationsResponse.data.message);
                } else {
                    this.$toasted.error(OrganisationsResponse.data.message);  
                }
            }, err => {
                //console.error(err);
            });
        },
        /**
         * Loads the Confirmation Modal 
         */
        showModal(organisation, action, index, action_messages) {
            this.modal_data.confirm_actions = action_messages;
            this.modal_data.organisation = organisation;
            this.modal_data.organisation_action = action;
            this.modal_data.organisation_index = index;
            this.modal_data.show_modal = true;
        },
        /**
         * Closes the Confirmation Modal 
         */
        hideModal() {
            this.modal_data.organisation = null;
            this.modal_data.organisation_action = null;
            this.modal_data.organisation_index = null;
            this.modal_data.confirm_actions = '';
            this.modal_data.show_modal = false;
        },
        sortingChanged(ctx) {
            this.sort_options.order_by = ctx.sortBy;
            this.sort_options.order_ascending = ctx.sortDesc;
            this.loadOrganisations();
        },
        filterChanged(value) {
            this.sort_options.filter = value;
            this.loadOrganisations();
        }
    },
    mounted() {
        let parsed_data = JSON.parse(this.organisation_data);
        this.items = parsed_data.data;
        this.current_page = parsed_data.current_page;
        this.per_page = parsed_data.per_page;
        this.total = parsed_data.total;
        this.path = parsed_data.path;
    }
}
</script>