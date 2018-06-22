<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
            <b-row class="justify-content-md-center">
            	<b-col cols="12">
	                <b-form @submit="submitDetails">
						<b-row v-if="display.show_expiry">
                            <b-col cols="3">
                            </b-col>
                            <b-col v-for="date in data.index_market_object.expiry_dates" cols="3">
                                <p><strong>{{ castToMoment(date) }}</strong></p>
                            </b-col>
                        </b-row>

						<b-form-group v-if="form_data.fields.length > 1" horizontal>
				      		<b-form-radio-group id="risky-choices" v-model="chosen_option" name="choice">
				        		<b-form-radio :disabled="display.disable_choice" value="0">CHOICE</b-form-radio>
				        		<b-form-radio :disabled="display.disable_choice" value="1">CHOICE</b-form-radio>
				      		</b-form-radio-group>
                    	</b-form-group>

						<b-row>
							<b-col cols="3">
								<label for="outright-strike-0">Strike</label>
							</b-col>
		      				<b-col v-for="field in form_data.fields" cols="3">
		      					<b-form-input id="outright-strike-0" 
		      						type="number"
		      						
									v-model="field.strike"
									>
		      					</b-form-input>
		      				</b-col>
						</b-row>

						<b-row>
							<b-col cols="3">
								<label for="outright-quantity-0">Quantity <span v-if="form_data.fields.length > 1"> (Ratio)</span></label>
							</b-col>
		      				<b-col v-for="field in form_data.fields" cols="3">
		      					<b-form-input id="outright-quantity-0" 
		      						type="number"
									min="0"
									v-model="field.quantity"
									required>
		      					</b-form-input>
		      				</b-col>
						</b-row>
	   					
	   					<b-row v-if="form_data.fields.length > 1">
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
        name: 'Details',
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
                this.form_data.fields.forEach( (element, index) => {
                    element.is_selected = (chosen_index == index) ? true : false;
                });
                console.log("YOU BE HER MATEE", this.form_data.fields);
            }
        },
        data() {
            return {
            	display:{
            		show_expiry: false,
            		disable_choice: false,
            	},
            	chosen_option: null,
                form_data: {
                	fields: []
                }
            };
        },
        methods: {
            submitDetails(evt) {
                evt.preventDefault();
                Vue.nextTick( () => {
                	console.log("DATA TO BE SENT", this.form_data);
                	this.callback(this.form_data);
				})
            },
            castToMoment(date_string) {
                return moment(date_string, 'YYYY-MM-DD HH:mm:ss').format('MMMYY');
            },
        },
        mounted() {
    		
    		this.form_data.fields.push({is_selected:true,strike: null,quantity: null});
            
            switch(this.data.index_market_object.trade_structure) {
            	case 'Outright':
            		this.display.disable_choice = true,
            		this.chosen_option = null;
            		break;
            	case 'Risky':
            		this.form_data.fields.push({is_selected:false,strike: null,quantity: null});
            		this.chosen_option = 0;
            		break;
            	case 'Fly':
            		this.display.disable_choice = true,
            		this.form_data.fields.push({is_selected:false,strike: null,quantity: null});
            		this.form_data.fields.push({is_selected:false,strike: null,quantity: null});
            		this.form_data.fields[2].is_selected = true;
            		break;
            	case 'Calendar':
            		this.display.show_expiry = true,
            		this.form_data.fields.push({is_selected:false,strike: null,quantity: null});
            		this.chosen_option = 0;
            		break;
            	default:

            }
        }
    }
</script>