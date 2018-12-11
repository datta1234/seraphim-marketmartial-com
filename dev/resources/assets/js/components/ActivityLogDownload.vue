<template>
    <form :action="action" :method="method">
        <!-- <div class="form-group row">
            <label for="email" class="col-2 col-form-label">Email address</label>
            <div class="col-10">
                <input type="email" class="form-control" id="email" placeholder="Enter email">
            </div>
        </div> -->
        <div class="form-group row">
            <label for="organisation" class="col-2 col-form-label">Organisation</label>
            <div class="col-10">
                <b-form-select name="organisation" v-model="org_id" :options="org_list" @change="loadUsers" :disabled="loading" placeholder="Select One..."/>
            </div>
        </div>
        <div class="form-group row">
            <label for="username" class="col-2 col-form-label">Username</label>
            <div class="col-10">
                <b-form-select name="user" v-model="user_id" :options="user_list" :disabled="loading || org_id == ''" placeholder="Select One..."/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-2 col-form-label">Date range</label>
            <div class="col-10">
                <div class="row">
                    <label for="start_date" class="col-2 col-form-label">From</label>
                    <div class="col-4">
                        <input name="start_date" type="date" class="form-control" id="start_date" :disabled="loading" placeholder="">
                    </div>
                    <label for="end_date" class="col-2 col-form-label">To</label>
                    <div class="col-4">
                        <input name="end_date" type="date" class="form-control" id="end_date" :disabled="loading" placeholder="">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="activity_type" class="col-2 col-form-label">Activity Type</label>
            <div class="col-10">
                <b-form-select name="activity_type" v-model="activity_type" :options="activity_type_list" :disabled="loading" placeholder="Select One..."/>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-10 offset-1">
                <button type="submit" class="btn mm-generic-trade-button w-100" :disabled="loading">Download Logs</button>
            </div>
        </div>
    </form>
</template>
<script>
    export default {
        props: {
            action: String,
            method: String,
            activityTypes: Object
        },
        data() {
            return {
                loading: false,
                org_list: {
                    '': "Select One..."
                },
                user_list: {
                    '': "Select One..."
                },
                org_id: '',
                user_id: '',
                activity_type: '',
            }
        },
        computed: {
            activity_type_list() {
                let act = this.activityTypes;
                act[''] = "Select One...";
                return act;
            }
        },
        methods: {
            loadOrganisations() {
                this.loading = true;
                axios.get('/admin/organisation')
                .then(response => {
                    this.loading = false;
                    this.org_list = response.data;
                    this.org_list[''] = "Select One..."
                })
                .catch(err => {
                    this.$toasted.error(err.data && err.data.message ? err.data.message : "Failed to load Organisation List");
                    this.loading = false;
                });
            },
            loadUsers(nV, oV) {
                if(nV == null) {
                    return;
                }
                this.loading = true;
                // wait for change to take place
                axios.get('/admin/organisation/'+nV+'/users')
                .then(response => {
                    this.loading = false;
                    this.user_list = response.data;
                    this.user_list[''] = "Select One..."
                })
                .catch(err => {
                    this.$toasted.error(err.data && err.data.message ? err.data.message : "Failed to load User List");
                    this.loading = false;
                });
            }
        },
        mounted() {
            this.loadOrganisations();
        }
    }
</script>