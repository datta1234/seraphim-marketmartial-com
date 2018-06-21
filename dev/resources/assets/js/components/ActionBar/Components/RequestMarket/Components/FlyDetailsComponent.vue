<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
            <b-row class="justify-content-md-center">
            	<b-col cols="12">
	                <b-form @submit="submitDetails">
						<b-row>
		      				<b-col class="text-center" cols="3" offset="3">
		      					<p>CHOICE</p>
		      				</b-col>
		      				<b-col class="text-center" cols="3" offset="3">
		      					<p>CHOICE</p>
		      				</b-col>
						</b-row>

						<b-row>
							<b-col cols="3">
								<label for="outright-strike-0">Strike</label>
							</b-col>
		      				<b-col cols="3">
		      					<b-form-input id="outright-strike-0" 
		      						type="number"
									v-model="form_data.options[0].strike"
									required>
		      					</b-form-input>
		      				</b-col>
		      				<b-col cols="3">
		      					<b-form-input id="outright-strike-1" 
		      						type="number"
									v-model="form_data.options[1].strike"
									required>
		      					</b-form-input>
		      				</b-col>
		      				<b-col cols="3">
		      					<b-form-input id="outright-strike-2" 
		      						type="number"
									v-model="form_data.options[2].strike"
									required>
		      					</b-form-input>
		      				</b-col>
						</b-row>

						<b-row>
							<b-col cols="3">
								<label for="outright-quantity-0">Quantity (Ratio)</label>
							</b-col>
		      				<b-col cols="3">
		      					<b-form-input id="outright-quantity-0" 
		      						type="number"
									v-model="form_data.options[0].quantity"
									required>
		      					</b-form-input>
		      				</b-col>
		      				<b-col cols="3">
		      					<b-form-input id="outright-quantity-1" 
		      						type="number"
									v-model="form_data.options[1].quantity"
									required>
		      					</b-form-input>
		      				</b-col>
		      				<b-col cols="3">
		      					<b-form-input id="outright-quantity-2" 
		      						type="number"
									v-model="form_data.options[2].quantity"
									required>
		      					</b-form-input>
		      				</b-col>
						</b-row>
	   					
	   					<b-row>
		   					<b-col class="text-center mt-3">	
		                		<p>
		                			All bids/offers going forward will have to maintain the ratio you set here
		                		</p>
		                	</b-col>
	   					</b-row>
	                    
	                    <b-form-group class="text-center">
	                        <b-button type="submit" class="mm-modal-market-button-alt w-25 mt-3">
	                            Submit
	                        </b-button>
	                    </b-form-group>
	                </b-form>
            	</b-col>
            </b-row>  
        </b-container>
    </div>
</template>

<script>
    export default {
        name: 'FlyDetails',
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
	                	{
	                		is_selected:true,
	                		strike: '',
	                    	quantity: '',
	                	}
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