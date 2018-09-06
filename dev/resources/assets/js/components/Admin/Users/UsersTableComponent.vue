<template>
    <div dusk="users-table" class="users-table">
            <b-form v-on:submit.prevent="" id="chat-message-form">
                <b-row>
                    <b-col cols="8">
                        <label class="mr-sm-2" for="admin-filter-users">Filter by Status:</label>
                        <b-form-select id="admin-filter-users"
                                       class="w-25"
                                       :options="filter_options"
                                       required
                                       v-model="sort_options.filter">
                        </b-form-select>
                    </b-col>
                    <b-col cols="4">
                        <slot></slot>
                        <button type="button" class="btn mm-generic-trade-button float-right ml-0 mr-2" @click="">
                            <font-awesome-icon icon="search"></font-awesome-icon>
                        </button>
                        <b-input class="w-50 float-right mr-0" id="admin-users-search" placeholder="Search" />
                    </b-col>
                </b-row>
            </b-form>
        <!-- Main table element -->
        <b-table v-if="users_loaded && items != null"
                 class="mt-2"
                 stacked="md"
                 :items="items"
                 :fields="fields">
            <template slot="organisation_title" slot-scope="row">
                {{ row.item.organisation.title }}
            </template>
            <template slot="is_invited" slot-scope="row">
                {{ row.item.is_invited ? 'Invited' : 'Signup'}}
            </template>
            <template slot="role_title" slot-scope="row">
                {{ row.item.role.title }}
            </template>
            <template slot="status" slot-scope="row">
                {{ userStatus(row.item) }}
            </template>
            <template slot="view" slot-scope="row">
                <button type="button" class="btn mm-generic-trade-button w-100">View</button>
            </template>
            <template slot="action" slot-scope="data">
                <button v-if="data.item.active && data.item.verified" 
                        type="button" 
                        class="btn mm-generic-trade-button w-100"
                        @click="showModal(data.item, {'active': false}, data.index, 'Deactivate')">
                    Deactivate
                </button>
                <button v-else-if="!data.item.active && data.item.verified" 
                        type="button" 
                        class="btn mm-generic-trade-button w-100"
                        @click="showModal(data.item, {'active': true}, data.index, 'Reactivate')">
                    Reactivate
                </button>
                <button v-else 
                        type="button" 
                        class="btn mm-generic-trade-button w-100"
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
    data () {
        return {
            items:  null,
            fields: [
                { key: 'full_name', label: 'Username' },
                { key: 'organisation_title', label: 'Organisation' },
                { key: 'email', label: 'Email' },
                { key: 'work_phone', label: 'Work' },
                { key: 'cell_phone', label: 'Mobile' },
                { key: 'is_invited', label: 'Type' },
                { key: 'role_title', label: 'Role' },
                { key: 'status', label: 'Status' },
                { key: 'view', label: 'View' },
                { key: 'action', label: 'Action' },
            ],
            current_page: 1,
            per_page: 10,
            total: 10,
            path: '',
            users_loaded: true,
            initial_load: 0,
            filter_options: [
                {text: "All", value: null},
                {text: "Active", value: null},
                {text: "Inactive", value: null},
                {text: "Request", value: null},
            ],
            sort_options: {
                filter: null
            },
            modal_data: {
                user_action: null,
                user: null,
                user_index: null,
                confirm_message: '',
                show_modal: false,
                modal_ref: 'confirm-action-modal',
            },
        }
    },
    computed: {

    },
    methods: {
        changePage($event) {
            this.current_page = $event;
            this.loadUsers();    
        },
        loadUsers() {
            axios.get(this.path + '?page='+this.current_page)
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
         * Loads the Reqeust a Market Modal 
         */
        showModal(user, action, index, message) {
            this.modal_data.confirm_message = message;
            this.modal_data.user = user;
            this.modal_data.user_action = action;
            this.modal_data.user_index = index;
            this.modal_data.show_modal = true;
            //this.$refs[this.modal_data.modal_ref].$on('hidden', this.hideModal);
        },
        /**
         * Closes the Reqeust a Market Modal 
         */
        hideModal() {
            this.modal_data.user = null;
            this.modal_data.user_action = null;
            this.modal_data.user_index = null;
            this.modal_data.confirm_message = '';
            this.modal_data.show_modal = false;
            //this.$refs[this.modal_data.modal_ref].$off('hidden', this.hideModal);
        },
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