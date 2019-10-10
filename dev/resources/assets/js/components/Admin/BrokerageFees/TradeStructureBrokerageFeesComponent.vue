<template>
    <div dusk="trade-struct-brokerage-fees" class="trade-struct-brokerage-fees" >
        <b-row v-if="brokerage_fee_data" class="mt-3">
            <b-col cols="12">
            	<b-card>
                    <b-row>
                        <b-col md="12">
                            <b-row v-if="errors.length > 0" class="mt-4">
                                <b-col cols="12">
                                    <div class="alert alert-danger" role="alert">
                                        Errors:
                                        <ul>
                                            <li :key="index" v-for="(error, index) in errors">
                                                <p class="text-danger mb-0">{{ error }}</p>
                                            </li>
                                        </ul>
                                    </div>
                                </b-col>
                            </b-row>
                        </b-col>
                    </b-row>
                	<b-form v-on:submit.prevent="" id="trade-struct-brokerage-fees-form">
	                    <b-row :key="index" v-for="(field, index) in brokerage_fee_data" class="mt-2">
	                    	<b-col cols="2" offset="3">
	                            <label class="mr-sm-2" :for="'brokerage-fee-field'+index">
                                   <strong>{{ field.title }}</strong>
                                </label>
	                        </b-col>
	                        <b-col cols="4">
	                            <b-input v-model="field.fee_percentage" class="w-100 mr-0" :id="'brokerage-fee-field'+index" placeholder="Enter Brokerage Fee...." />
	                        </b-col>
	                    </b-row>
	                    <b-row>
		                    <b-col cols="2" offset="10">
		                        <button type="submit" 
		                                class="btn  mm-generic-trade-button w-100 mt-5" 
		                                @click="saveTradeStructureBrokerageFees()">
		                            Update
		                        </button>
		                    </b-col>
	                    </b-row>
	                </b-form>
            	</b-card>
            </b-col>
        </b-row>
        <b-row v-else class="mt-5">
        	<b-col cols="12">
        		<b-card class="text-center">
	        		<p>There are no available Trade Structures to set Brokerage Fees on.</p>
	        	</b-card>
        	</b-col>
        </b-row>

    </div>
</template>

<script>
    export default {
    	name: 'TradeStructureBrokerageFees',
    	props: [
            'tradeStructures',
        ],
        data() {
            return {
                brokerage_fee_data: null,
                errors: [],
            };
        },
        methods: {
        	saveTradeStructureBrokerageFees() {
        		axios.post(axios.defaults.baseUrl + '/admin/brokerage-fees/trade-structures',
        			{"trade_structures" : this.brokerage_fee_data})
                .then(brokerageFeesResponse => {
                	this.$toasted.success(brokerageFeesResponse.data.message);
                    this.errors = [];      
                }).catch( (err) => {
                    this.errors = [];
                    // Handles request validation errors
                    if(err.errors) {
                        Object.keys(err.errors).forEach(error => {
                            console.log("ERR ", err.errors[error]);
                            if(Array.isArray(err.errors[error])) {

                                this.errors = this.errors.concat(err.errors[error]);
                            } else {
                                this.errors.push(err.errors[error]);
                            }
                        });
                        console.log(err.errors,this.errors);
                    }
                    if(err.message) {
                        // Handles default sent errors
                        this.$toasted.error(err.message);
                    }
                    if(err.data && err.data.message) {
                    // Handles default sent errors
                        this.$toasted.error(err.data.message);
                    }
                });
        	}
        },
        mounted() {
            if(this.tradeStructures) {
                this.brokerage_fee_data =JSON.parse(this.tradeStructures);
            }
        }
    }
</script>