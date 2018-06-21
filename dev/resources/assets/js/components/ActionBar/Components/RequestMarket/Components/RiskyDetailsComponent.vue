<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
            <b-row class="justify-content-md-center">
                <b-form @submit="submitDetails">
                    <b-form-group horizontal>
				      	<b-form-radio-group id="risky-choices" v-model="chosen_option" name="choice">
				        	<b-form-radio value="0">CHOICE</b-form-radio>
				        	<b-form-radio value="1">CHOICE</b-form-radio>
				      	</b-form-radio-group>
                    </b-form-group>

                    <b-form-group horizontal label="Strike" label-for="outright-strike-0">
                        <b-form-input id="outright-strike-0"
                                  type="number"
                                  v-model="form_data.options[0].strike"
                                  required>
                        </b-form-input>
                        <b-form-input id="outright-strike-1"
                                  type="number"
                                  v-model="form_data.options[1].strike"
                                  required>
                        </b-form-input>
                    </b-form-group>

                    <b-form-group horizontal label="Quantity (Ratio)" label-for="outright-quantity-0">
                        <b-form-input id="outright-quantity-0"
                                  type="number"
                                  v-model="form_data.options[0].quantity"
                                  placeholder="500"
                                  required>
                        </b-form-input>
                        <b-form-input id="outright-quantity-1"
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
        watch: {
            'chosen_option': function(chosen_index) {
                this.form_data.options.forEach( (element, index) => {
                    element.is_selected = (chosen_index == index) ? true : false;
                });
                console.log("YOU BE HER MATEE", this.form_data.options);
            }
        },
        data() {
            return {
                chosen_option: 0,
                form_data: {
                	options: [
	                	{
                            is_selected:true,
	                		strike: '',
	                    	quantity: '',
	                	},
	                	{
                            is_selected:false,
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