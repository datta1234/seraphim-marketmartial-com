<template>
    <div dusk="edit-rebate" class="edit-rebate" >
    	<button type="button" 
                class="btn mm-generic-trade-button w-100"
                @click="showModal()">
            Edit
        </button>

        <!-- Confirmations Modal -->
        <b-modal class="mm-modal mx-auto" v-model="modal_data.show_modal" :ref="modal_data.modal_ref">
            <!-- Modal title content --> 
            <div class="mm-modal-title" slot="modal-title">
                Edit Rebate
            </div>
            <!-- Modal body content -->
            <b-form v-on:submit.prevent="" id="chat-message-form">
            	<b-row class="mt-2">
	                <b-col cols="4">
	                    <label class="mr-sm-2" for="new-rebate-amount">Old Rebate Amount:</label>
	                </b-col>
	                <b-col cols="8">
	                    <p class="pl-2">{{ splitValHelper(item_data.rebate, ' ', 3) }}</p>
	                </b-col>
	            </b-row>
	            <b-row class="mt-2">
	                <b-col cols="4">
	                    <label class="mr-sm-2" for="new-rebate-amount">New Rebate Amount:</label>
	                </b-col>
	                <b-col cols="8">
	                    <b-input v-model="modal_data.rebate" class="w-100 mr-0" id="new-rebate-amount" placeholder="New Rebate Value" />
	                </b-col>
	            </b-row>
	        </b-form>

	        <b-row v-if="modal_data.errors.length > 0" class="text-center mt-4">
                <b-col :key="index" v-for="(error, index) in modal_data.errors" cols="12">
                    <p class="text-danger mb-0">{{ error }}</p>
                </b-col>
            </b-row>

            <!-- Modal footer content -->
            <div slot="modal-footer" class="w-100">
                <b-row align-v="center">
                    <b-col cols="12">
                        <b-button class="mm-modal-button ml-2 w-25" @click="rebateAction()">Update</b-button>
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
    	name: 'EditRebate',
    	props: [
            'item_index',
            'item_data',
            'callback',
        ],
        data() {
            return {
            	rebate: 0,
            	modal_data: {
            		errors:[],
                    rebate: null,
                    rebate_index: null,
                    show_modal: false,
                    modal_ref: 'edit-rebate-modal',
                },
            };
        },
        methods: {
            rebateAction() {
                let index = this.modal_data.rebate_index;
                axios.put(axios.defaults.baseUrl + '/admin/rebates/'+this.item_data.id,
                	{new_amount: this.modal_data.rebate})
                .then(rebatesResponse => {
                	console.log(rebatesResponse);
                	this.callback(rebatesResponse.data.data.rebate, this.modal_data.rebate_index);
                    this.hideModal();
                    this.$toasted.success(rebatesResponse.data.message);
                }, err => {
                	err.errors.new_amount.forEach(error => {
                		this.modal_data.errors.push(error);
                	});
                    this.$toasted.error(err.message);
                    console.error(err);
                });
            },
            /**
             * Loads the Confirmation Modal 
             */
            showModal() {
            	console.log("index: ",this.item_index);
            	console.log("Data: ", this.item_data);
                this.modal_data.rebate = this.item_data.rebate;
                this.modal_data.rebate_index = this.item_index;
                this.modal_data.show_modal = true;
                this.$refs[this.modal_data.modal_ref].$on('hidden', this.hideModal);
            },
            /**
             * Closes the Confirmation Modal 
             */
            hideModal() {
                this.modal_data.rebate = null;
                this.modal_data.rebate_index = null;
                this.modal_data.errors = [];
                this.modal_data.show_modal = false;
                this.$refs[this.modal_data.modal_ref].$off('hidden', this.hideModal);
            },
        },
        mounted() {
        }
    }
</script>