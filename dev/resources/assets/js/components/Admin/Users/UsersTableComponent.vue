<template>
    <div dusk="users-table" class="users-table">
        <b-form v-on:submit.prevent="" id="chat-message-form">
            <b-row>
                <b-col cols="8">
                    <label class="mr-sm-2" for="admin-filter-users">Filter by Status:</label>
                    <b-form-select id="admin-filter-users"
                                   class="w-25"
                                   :options="filter_options"
                                   v-model="sort_options.filter"
                                   @change="filterChanged">
                    </b-form-select>
                </b-col>
                <b-col cols="4">
                    <!-- <create-user></create-user> -->
                    <slot></slot>
                    <button type="submit" 
                            class="btn mm-generic-trade-button float-right ml-0 mr-2" 
                            @click="searchTerm">
                        <i class="fas fa-search"></i>
                    </button>
                    <b-input v-model="sort_options.search" class="w-50 float-right mr-0" id="admin-users-search" placeholder="Search" />
                </b-col>
            </b-row>
        </b-form>
        <!-- Main table element -->
        <b-table v-if="users_loaded && items != null"
                 class="mt-2 admin-users-table"
                 stacked="md"
                 :items="items"
                 :fields="fields"
                 :sort-by.sync="sort_options.order_by"
                 :sort-desc.sync="sort_options.order_ascending"
                 :no-local-sorting="true"
                 @sort-changed="sortingChanged">
            <template slot="is_online" slot-scope="row">
                <span v-bind:class="[ row.item.is_online ? 'online-status icon-mm-logo-v2' : 'offline-status icon-sitting-bird']" class="icon"></span>
                <!-- <span v-bind:class="[ row.item.is_online ? 'status-dot online-dot' : 'status-dot offline-dot']"></span> -->
            </template>     
            <template slot="organisation_title" slot-scope="row">
                {{ row.item.organisation.title }}
            </template>
            <template slot="is_invited" slot-scope="row">
                {{ row.item.is_invited ? 'Invited' : 'Signup'}}
            </template>
            <template slot="role_id" slot-scope="row">
                {{ row.item.role.title }}
            </template>
            <template slot="status" slot-scope="row">
                {{ userStatus(row.item) }}
            </template>
            <template slot="view" slot-scope="row">
                <a :href="base_url + '/admin/user/'+ row.item.id" class="btn mm-generic-trade-button w-100">View</a>
            </template>
            <template slot="action" slot-scope="data">
                <b-form-select  v-if="data.item.active && data.item.verified" 
                        class="form-control" 
                        @change="actionChanged(data.item, data.index, $event)"
                        v-model="action_selected">
                        <option selected disabled value="select">Select</option>
                        <option v-for="option in action_options" v-bind:value="option.value">{{ option.text }}</option>
                </b-form-select >
                <button v-else-if="!data.item.active && data.item.verified" 
                        type="button" 
                        class="btn mm-generic-trade-button w-100"
                        @click="showModal(data.item, {'active': true}, data.index, 'Reactivate')">
                    Reactivate
                </button>
                <button v-else 
                        type="button" 
                        class="btn mm-generic-trade-button w-100"
                        :disabled="!data.item.tc_accepted"
                        @click="showModal(data.item, {'verified': true}, data.index, 'Verify')">
                    Verify
                </button>
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

            <b-row v-if="modal_data.user" class="justify-content-md-center">
                <b-col class="text-center" cols="12">
                    <p class="modal-info-text">Are you sure you want to {{ modal_data.confirm_message }} {{ modal_data.user.full_name }}?</p>
                </b-col>
            </b-row>
            <b-row v-if="modal_data.user && modal_data.user_action.verified && modal_data.user.organisation.verified == 1" class="justify-content-md-center">
                <b-col class="text-center" cols="12">
                    <p class="modal-info-text">Note Verifying this user will verify the following organisation as well:<br>{{modal_data.user.organisation.title}}</p>
                </b-col>
            </b-row>

            <!-- Modal footer content -->
            <div slot="modal-footer" class="w-100">
                <b-row align-v="center">
                    <b-col cols="12">
                        <b-button class="mm-modal-button ml-2 w-25" @click="userAction()">Ok</b-button>
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
            'user_data',
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
                { key: 'is_online', label: 'Online' },
                { key: 'status', label: 'Status' },
                { key: 'full_name', label: 'Username', sortable: true, sortDirection: 'desc' },
                { key: 'organisation_title', label: 'Organisation', sortable: true, sortDirection: 'desc' },
                { key: 'email', label: 'Email', sortable: true, sortDirection: 'desc' },
                { key: 'work_phone', label: 'Work', sortable: true, sortDirection: 'desc' },
                { key: 'cell_phone', label: 'Mobile', sortable: true, sortDirection: 'desc' },
                { key: 'is_invited', label: 'Type', sortable: true, sortDirection: 'desc' },
                { key: 'role_id', label: 'Role', sortable: true, sortDirection: 'desc' },
                { key: 'view', label: 'View' },
                { key: 'action', label: 'Action' },
            ],
            current_page: 1,
            per_page: 10,
            total: 10,
            path: '',
            users_loaded: true,
            initial_load: 0,
            action_selected: 'select',
            filter_options: [
                {text: "All", value: null},
                {text: "Active", value: 'active'},
                {text: "Inactive", value: 'inactive'},
                {text: "Request", value: 'request'},
            ],
            sort_options: {
                search: '',
                filter: null,
                order_by: null,
                order_ascending: true,
            },
            modal_data: {
                user_action: null,
                user: null,
                user_index: null,
                confirm_message: '',
                show_modal: false,
                modal_ref: 'confirm-action-modal',
            },
            action_options: [
                {text: "Impersonate", value: 'impersonate'},
                {text: "Deactivate", value: 'deactivate'},
            ],            
        }
    },
    methods: {
        changePage($event) {
            this.current_page = $event;
            this.loadUsers();    
        },
        searchTerm() {
            this.current_page = 1;
            this.loadUsers();
        },
        loadUsers() {
            axios.get(this.path, {
                params:{
                    'page': this.current_page,
                    'search': this.sort_options.search,
                    '_order_by': (this.sort_options.order_by !== null ? this.sort_options.order_by : ''),
                    '_order': (this.sort_options.order_ascending ? 'ASC' : 'DESC'),
                    'filter': (this.sort_options.filter !== null ? this.sort_options.filter : ''),
                }
            })
            .then(usersResponse => {
                if(usersResponse.status == 200) {
                    this.current_page = usersResponse.data.current_page;
                    this.per_page = usersResponse.data.per_page;
                    this.total = usersResponse.data.total;
                    this.items = usersResponse.data.data;
                    /*EventBus.$emit('loading', 'requestDates');
                    this.dates_loaded = true;*/
                    this.users_loaded = true;
                } else {
                    console.error(err);    
                }
            }, err => {
                console.error(err);
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
        userAction() {
            let index = this.modal_data.user_index;
            axios.put(axios.defaults.baseUrl + '/admin/user/'+this.modal_data.user.id, this.modal_data.user_action)
            .then(usersResponse => {
                if(usersResponse.status == 200) {
                    this.items[index].active = usersResponse.data.data.active;
                    this.items[index].verified = usersResponse.data.data.verified;
                    this.hideModal();
                    this.$toasted.success(usersResponse.data.message);
                } else {
                    this.$toasted.error(usersResponse.data.message);  
                }
            }, err => {
                console.error(err);
            });
        },
        /**
         * Loads the Confirmation Modal 
         */
        showModal(user, action, index, message) {
            this.modal_data.confirm_message = message;
            this.modal_data.user = user;
            this.modal_data.user_action = action;
            this.modal_data.user_index = index;
            this.modal_data.show_modal = true;
        },
        /**
         * Closes the Confirmation Modal 
         */
        hideModal() {
            this.modal_data.user = null;
            this.modal_data.user_action = null;
            this.modal_data.user_index = null;
            this.modal_data.confirm_message = '';
            this.modal_data.show_modal = false;
        },
        sortingChanged(ctx) {
            this.sort_options.order_by = ctx.sortBy;
            this.sort_options.order_ascending = ctx.sortDesc;
            this.loadUsers();
        },
        filterChanged(value) {
            this.sort_options.filter = value;
            this.loadUsers();
        },
        actionChanged(user, index, action) {
            if (action == 'deactivate') {
                this.showModal(user,{'active': false},index,'Deactivate');
            } else if (action == 'impersonate') {
                window.location = '/impersonate/take/'+user.id;
            }
        }

    },
    mounted() {
        let parsed_data = JSON.parse(this.user_data);
        this.items = parsed_data.data;
        this.current_page = parsed_data.current_page;
        this.per_page = parsed_data.per_page;
        this.total = parsed_data.total;
        this.path = parsed_data.path;
    }
}
</script>