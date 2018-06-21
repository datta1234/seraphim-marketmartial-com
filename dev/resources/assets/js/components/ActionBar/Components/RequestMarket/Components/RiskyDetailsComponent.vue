<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
            <b-row class="justify-content-md-center">
                <b-form @submit="submitDetails">
                    <b-form-group horizontal label="Strike:" label-for="outright-strike-1">
				      	<b-form-radio-group id="risky-choices" v-model="form_data.choice" name="choice">
				        	<b-form-radio value="0">CHOICE</b-form-radio>
				        	<b-form-radio value="1">CHOICE</b-form-radio>
				      	</b-form-radio-group>
                    </b-form-group>

                    <b-form-group horizontal label="Strike:" label-for="outright-strike-a">
                        <b-form-input id="outright-strike-a"
                                  type="number"
                                  v-model="form_data.options[0].strike"
                                  required>
                        </b-form-input>
                        <b-form-input id="outright-strike-b"
                                  type="number"
                                  v-model="form_data.options[1].strike"
                                  required>
                        </b-form-input>
                    </b-form-group>

                    <b-form-group horizontal label="Quantity" label-for="outright-quantity-a">
                        <b-form-input id="outright-quantity-a"
                                  type="number"
                                  v-model="form_data.options[0].quantity"
                                  placeholder="500"
                                  required>
                        </b-form-input>
                        <b-form-input id="outright-quantity-b"
                                  type="number"
                                  v-model="form_data.options[1].quantity"
                                  placeholder="500"
                                  required>
                        </b-form-input>
                    </b-form-group>
   
                	<p>All bids/offers going forward will have to maintain the ratio you set here</p>
                    
                    <b-form-group>
                        <b-button type="submit" class="mm-modal-market-button-alt w-100 mt-3">
                            Submit
                        </b-button>
                    </b-form-group>
                </b-form>
            </b-row>  
        </b-container>
    </div>
</template>

<script>
    export default {
        name: 'RiskyDetails',
        props:{
            'callback': {
                type: Function
            },
            'data': {
                type: Object
            }
        },
        data() {
            return {
                form_data: {
                	choice: 0,
                	options: [
	                	{
	                		strike: '',
	                    	quantity: '',
	                	},
	                	{
	                		strike: '',
	                    	quantity: '',	
	                	},
                	]
                }
            };
        },
        methods: {
            submitDetails(evt) {
                evt.preventDefault();
                this.callback(this.form_data);
            },
        },
        mounted() {
            
        }
    }
</script>