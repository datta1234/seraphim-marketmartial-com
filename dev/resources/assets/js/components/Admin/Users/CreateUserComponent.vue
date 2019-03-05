<template>
    <div dusk="create-user" class="create-user">
        <button type="button" class="btn mm-generic-trade-button float-right" @click="showModal()">Create User</button>       

        <!-- New User Modal -->
        <b-modal class="mm-modal mx-auto" size="lg" v-model="modal_data.show_modal" :ref="modal_data.modal_ref">
            <!-- Modal title content --> 
            <div class="mm-modal-title" slot="modal-title">
                Create New User
            </div>

            <b-row>
                <b-col cols="12">
                    <b-form v-on:submit.prevent="" id="user-create-form">
                        <b-row align-h="center">
                            <b-col cols="3">
                                <label for="full_name">Full Name</label>
                            </b-col>
                            <b-col cols="4">
                                <b-form-input id="full_name" 
                                    v-model="form_data.full_name"
                                    required
                                    placeholder="Bob Smith">
                                </b-form-input>
                            </b-col>
                        </b-row>

                        <b-row align-h="center">
                            <b-col cols="3">
                                <label for="email">Email Address</label>
                            </b-col>
                            <b-col cols="4">
                                <b-form-input id="email" 
                                    v-model="form_data.email"
                                    required
                                    placeholder="bobsmith@bankabc.com">
                                </b-form-input>
                            </b-col>
                        </b-row>

                        <b-row align-h="center">
                            <b-col cols="3">
                                <label for="work_phone">Work Phone</label>
                            </b-col>
                            <b-col cols="4">
                                <b-form-input id="work_phone" 
                                    v-model="form_data.work_phone"
                                    required
                                    placeholder="011 222 3333">
                                </b-form-input>
                            </b-col>
                        </b-row>

                        <b-row align-h="center">
                            <b-col cols="3">
                                <label for="cell_phone">Mobile Phone</label>
                            </b-col>
                            <b-col cols="4">
                                <b-form-input id="cell_phone" 
                                    v-model="form_data.cell_phone"
                                    required
                                    placeholder="012 345 6789">
                                </b-form-input>
                            </b-col>
                        </b-row>

                        <b-row align-h="center">
                            <b-col cols="3">
                                <label for="organisation">Organisation</label>
                            </b-col>
                            <b-col cols="4">
                                <b-form-input id="organisation" 
                                    v-model="form_data.organisation"
                                    required
                                    placeholder="Bank ABC">
                                </b-form-input>
                            </b-col>
                        </b-row>

                        <b-row align-h="center">
                            <b-col cols="3">
                                <label for="role">User Role</label>
                            </b-col>
                            <b-col cols="4">
                                <b-form-select v-model="form_data.role" :options="roles" class="mb-3" />
                                <b-form-input id="role" 
                                    v-model="form_data.role"
                                    required
                                    placeholder="Select Role">
                                </b-form-input>
                            </b-col>
                        </b-row>

                        <b-row v-if="errors.messages.length > 0" class="text-center mt-4">
                            <b-col v-for="error in errors.messages" cols="12">
                                <p class="text-danger mb-0">{{ error }}</p>
                            </b-col>
                        </b-row>
                        
                        <b-form-group class="text-center mt-4 mb-0">
                            <button @click="" type="submit" class="btn mm-generic-trade-button mr-2">Populate and Add</button>
                            <button @click="" type="submit" class="btn mm-generic-trade-button">Add User Now</button>
                        </b-form-group>
                    </b-form>    
                </b-col>
            </b-row>

            <!-- Modal footer content -->
            <div slot="modal-footer" class="w-100">
                <b-row>
                    <b-col cols="12">
                        <b-button class="mm-modal-button float-right ml-2 w-25" @click="hideModal()">Close</b-button>
                    </b-col>
                </b-row>
           </div>
        </b-modal>
        <!-- END Confirmations Modal -->
    </div>
</template>

<script>
export default {
    data () {
        return {
            modal_data: {
                show_modal: false,
                modal_ref: 'create-user-modal',
            },
            errors: {
                messages: [],
            },
            form_data: {
                full_name:'',
                email:'',
                work_phone:'',
                cell_phone:'',
                organisation:'',
                role:'',
            },
            organisations: null,
            roles: [
                { value:null, text:"" }
            ],
        }
    },
    methods: {
        /**
         * Loads the Reqeust a Market Modal 
         */
        showModal() {
            this.modal_data.show_modal = true;
        },
        /**
         * Closes the Reqeust a Market Modal 
         */
        hideModal() {
            this.modal_data.show_modal = false;
        },
        loadCreateData() {
            axios.get(axios.defaults.baseUrl + '/admin/user/create')
            .then(createDataResponse => {
                if(createDataResponse.status == 200) {
                    this.organisations = createDataResponse.data.data.roles;
                    this.organisations = createDataResponse.data.data.organisations;
                } else {
                    //console.error(err);    
                }
            }, err => {
                //console.error(err);
            });
        }
    },
    mounted() {
        this.loadCreateData();
    }
}
</script>