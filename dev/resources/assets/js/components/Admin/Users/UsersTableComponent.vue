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
                        @click="userAction(data.item, {'active': false}, data.index)">
                    Deactivate
                </button>
                <button v-else-if="!data.item.active && data.item.verified" 
                        type="button" 
                        class="btn mm-generic-trade-button w-100"
                        @click="userAction(data.item, {'active': true}, data.index)">
                    Reactivate
                </button>
                <button v-else 
                        type="button" 
                        class="btn mm-generic-trade-button w-100"
                        @click="userAction(data.item, {'verified': true}, data.index)">
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
            }
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
        userAction(user, action, index) {
            axios.put(axios.defaults.baseUrl + '/admin/user/'+user.id, action)
            .then(usersResponse => {
                if(usersResponse.status == 200) {
                    console.log("EDIT from server: ",usersResponse);
                    // @TODO SHOW TOAST USER MESSAGES
                    this.items[index].active = usersResponse.data.data.active;
                    this.items[index].verified = usersResponse.data.data.verified;
                } else {
                    console.error(err);    
                }
            }, err => {
                console.error(err);
            });
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