<template>
    <div dusk="detail" class="step-selections">
        <b-container fluid>
            <b-row class="justify-content-md-center">
            	<b-col cols="12">
	                <b-form @submit="submitDetails">
						<b-row v-if="display.show_expiry" align-h="center">
                            <b-col v-for="(date,key) in data.index_market_object.expiry_dates" 
                                    cols="3" 
                                    :offset="(key == 0)? 3:0"
                                    class="text-center">
                                <p><strong>{{ castToMoment(date) }}</strong></p>
                            </b-col>
                        </b-row>
                        
                        <b-form-radio-group v-if="form_data.fields.length > 1" 
                                            id="risky-choices" 
                                            v-model="chosen_option" 
                                            name="choice"
                                            class="mb-2">
                            <b-row align-h="center">
                                <b-col cols="3" offset="3" class="text-center">    
                                    <b-form-radio :disabled="display.disable_choice" value="0">CHOICE</b-form-radio>
                                </b-col>
                                <b-col cols="3" :offset="(form_data.fields.length == 3)? 3: 0" class="text-center">
                                    <b-form-radio :disabled="display.disable_choice" value="1">CHOICE</b-form-radio>    
                                </b-col>
                            </b-row>
		      		    </b-form-radio-group>

						<b-row align-h="center">
							<b-col cols="3">
								<label for="outright-strike-0">Strike</label>
							</b-col>
		      				<b-col v-for="field in form_data.fields" cols="3">
		      					<b-form-input id="outright-strike-0" 
		      						type="number"
		      						min="0"
									v-model="field.strike"
									required>
		      					</b-form-input>
		      				</b-col>
						</b-row>

						<b-row align-h="center">
							<b-col cols="3">
								<label for="outright-quantity-0">Quantity <span v-if="form_data.fields.length > 1"> (Ratio)</span></label>
							</b-col>
		      				<b-col v-for="field in form_data.fields" cols="3">
		      					<b-form-input id="outright-quantity-0" 
		      						type="number"
									min="0"
									v-model="field.quantity"
                                    placeholder="500"
									required>
		      					</b-form-input>
                                <p v-if="field.quantity < 500" class="modal-warning-text text-danger text-center">
                                    *Warning: The recommended minimum quantity is 500.
                                </p>
		      				</b-col>
						</b-row>
	   					
	   					<b-row v-if="form_data.fields.length > 1">
		   					<b-col class="text-center mt-3">	
		                		<p class="modal-info-text">
		                			All bids/offers going forward will have to maintain the ratio you set here
		                		</p>
		                	</b-col>
	   					</b-row>
	                    
	                    <b-form-group class="text-center mt-4 mb-0">
	                        <b-button type="submit" class="mm-modal-market-button-alt w-50">
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
    		
    		this.form_data.fields.push({is_selected:true,strike: null,quantity: 500});
            
            switch(this.data.index_market_object.trade_structure) {
            	case 'Outright':
            		this.display.disable_choice = true,
            		this.chosen_option = null;
            		break;
            	case 'Risky':
            		this.form_data.fields.push({is_selected:false,strike: null,quantity: 500});
            		this.chosen_option = 0;
            		break;
            	case 'Fly':
            		this.display.disable_choice = true,
            		this.form_data.fields.push({is_selected:false,strike: null,quantity: 500});
            		this.form_data.fields.push({is_selected:false,strike: null,quantity: 500});
            		this.form_data.fields[2].is_selected = true;
            		break;
            	case 'Calendar':
            		this.display.show_expiry = true,
            		this.form_data.fields.push({is_selected:false,strike: null,quantity: 500});
            		this.chosen_option = 0;
            		break;
            	default:

            }
        }
    }
</script>