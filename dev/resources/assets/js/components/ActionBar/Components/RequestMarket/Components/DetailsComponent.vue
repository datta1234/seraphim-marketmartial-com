<template>
    <div dusk="details" class="details">
        <b-container fluid>
            <b-row class="justify-content-md-center">
            	<b-col cols="12">
	                <b-form @submit="submitDetails" id="index-details-form">
						<b-row v-if="display.show_expiry" align-h="center">
                            <b-col :cols="data.market_object.stock ?  11 : 12">
                                <b-row align-h="center">
                                    <b-col :key="key" v-for="(date,key) in data.market_object.expiry_dates" 
                                            cols="3" 
                                            :offset="(key == 0)? 3:0"
                                            class="text-center">
                                        <p><strong>{{ castToMoment(date) }}</strong></p>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col v-if="data.market_object.stock" cols="1"></b-col>
                        </b-row>
                        
                        <b-row v-if="display.versus" align-h="center">
                            <b-col cols="3" offset="3" class="text-center">    
                                <h4>{{ data.market_object.markets[0].title }}</h4>
                            </b-col>
                            <b-col cols="1">
                                <h4>VS.</h4>
                            </b-col>
                            <b-col cols="3"class="text-center">    
                                <h4>{{ data.market_object.markets[1].title }}</h4>
                            </b-col>
                        </b-row>

                        <b-row v-if="form_data.fields.length > 1" align-h="center">
                            <b-col :cols="data.market_object.stock ?  11 : 12">
                                <b-row align-h="center">
                                    <b-col v-if="display.disable_choice" cols="12 mb-2">
                                        <b-row>
                                            <b-col cols="3" offset="3" class="text-center">    
                                                <b-badge variant="info details-choice-badge">CHOICE</b-badge>
                                            </b-col>
                                            <b-col cols="3" :offset="(form_data.fields.length == 3)? 3: 0" class="text-center">
                                                <b-badge variant="info details-choice-badge">CHOICE</b-badge>    
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                    <b-col v-else cols="12">
                                        <b-form-radio-group id="risky-choices" 
                                                            v-model="chosen_option" 
                                                            name="choice"
                                                            class="mb-2">
                                            <b-row align-h="center">
                                                <b-col cols="3" offset="3" class="text-center">    
                                                    <b-form-radio id="choice-0" :disabled="display.disable_choice" value="0">CHOICE</b-form-radio>
                                                </b-col>
                                                <b-col  cols="3" 
                                                        :offset="(form_data.fields.length == 3)? 3
                                                            : (display.versus? 1 : 0)"
                                                        class="text-center">
                                                    <b-form-radio id="choice-1" :disabled="display.disable_choice" value="1">CHOICE</b-form-radio>    
                                                </b-col>
                                            </b-row>
                		      		    </b-form-radio-group>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col v-if="data.market_object.stock" cols="1">
                                
                            </b-col>
                        </b-row>

						<b-row v-if="display.has_strike" align-h="center">
                            <b-col :cols="data.market_object.stock ?  11 : 12">
                                <b-row align-h="center">
        							<b-col cols="3">
        								<label for="strike-0">Strike</label>
        							</b-col>
        		      				<b-col :key="index" v-for="(field, index) in form_data.fields" cols="3">
        		      					<b-form-input :id="'strike-'+index" 
        									v-model="field.strike"
                                            :state="inputState(index, 'Strike')"
        									required>
        		      					</b-form-input>
        		      				</b-col>
                                </b-row>
                            </b-col>
                            <b-col v-if="data.market_object.stock" cols="1">
                                <label for="strike-0">ZAR</label>
                            </b-col>
						</b-row>

						<b-row align-h="center">
                            <b-col :cols="data.market_object.stock ?  11 : 11">
                                <b-row align-h="center">
            						<b-col cols="3">
            							<label for="quantity-0">
                                            {{ display.is_vega ? 'Vega': 'Quantity'}} 
                                            <span v-if="display.is_ratio"> (Ratio)</span>
                                        </label>
            						</b-col>
            	      				<b-col  :key="index" v-for="(field, index) in form_data.fields"
                                            cols="3" 
                                            :offset="(display.versus && index != 0)? 1 : 0">
            	      					<b-form-input :id="'quantity-'+index" 
            	      						type="number"
            								min="0"
            								v-model="field.quantity"
                                            placeholder="500"
                                            :state="inputState(index, 'Quantity')"
            								required>
            	      					</b-form-input>
                                        <p  v-if="field.quantity < field.quantity_default"
                                            class="modal-warning-text text-danger text-center">
                                            *Warning: The recommended minimum quantity is {{ field.quantity_default }}.
                                        </p>
            	      				</b-col>
                                </b-row>
                            </b-col>
                            <b-col v-if="data.market_object.stock" cols="1">
                                <label for="quantity-0">Rm</label>
                            </b-col>
                            <b-col v-if="display.is_vega" cols="1">
                                <label for="quantity-0">ZAR</label>
                            </b-col>
						</b-row>

                        <b-row v-if="display.has_capped" align-h="center">
                            <b-col :cols="data.market_object.stock ?  11 : 11">
                                <b-row align-h="center">
                                    <b-col cols="3">
                                        <label for="capped-0">Capped</label>
                                    </b-col>
                                    <b-col  :key="index" v-for="(field, index) in form_data.fields"
                                            cols="3" 
                                            :offset="(display.versus && index != 0)? 1 : 0">
                                        <b-form-input :id="'capped-'+index" 
                                            type="number"
                                            min="0"
                                            v-model="field.cap"
                                            placeholder="500"
                                            :state="inputState(index, 'Cap')"
                                            required>
                                        </b-form-input>
                                        <p  v-if="field.cap < field.cap_default"
                                            class="modal-warning-text text-danger text-center">
                                            *Warning: The recommended minimum quantity is {{ field.cap_default }}.
                                        </p>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col cols="1">
                                <label for="capped-0">x</label>
                            </b-col>
                        </b-row>

                        <b-row v-if="display.is_ratio">
                            <b-col class="text-center mt-3">    
                                <p class="modal-info-text">
                                    All trades will maintain the above ratio.
                                </p>
                            </b-col>
                        </b-row>

                        <b-row v-if="errors.messages.length > 0" class="text-center mt-4">
                            <b-col :key="index" v-for="(error, index) in errors.messages" cols="12">
                                <p class="text-danger mb-0">{{ error }}</p>
                            </b-col>
                        </b-row>
	                    
	                    <b-form-group class="text-center mt-4 mb-0">
	                        <b-button id="submit-index-details" type="submit" class="mm-modal-market-button-alt w-50">
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
            },
            'errors': {
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
                    has_strike: true,
                    versus: false,
                    is_ratio: false,
                    is_vega: false,
                    has_capped: false,
            	},
            	chosen_option: null,
                form_data: {
                	fields: []
                },
                quantity_default: {
                    TOP40: 500,
                    DTOP: 2500,
                    DCAP: 1500,
                    stock: 50,
                },
                cap_default: 2.5,
            };
        },
        methods: {
            /**
             * Sets the form data to the passed data object and calls the
             *  component call back method.
             *
             * @param {Event} evt - form submit event
             */
            submitDetails(evt) {
                evt.preventDefault();
                Vue.nextTick( () => {
                    /*this.data.market_object.details = this.form_data;*/
                	this.callback('',this.form_data);
				})
            },
            /**
             * Casting a passed string to moment with a new format
             *
             * @param {string} date_string
             */
            castToMoment(date_string) {
                return moment(date_string, 'YYYY-MM-DD HH:mm:ss').format('MMMYY');
            },
            /**
             * Toggles input states when there are errors for the input
             *
             * @param {number} index - the index of the input
             * @param {string} type - the type of input
             */
            inputState(index, type) {
                return (this.errors.fields.indexOf('trade_structure_groups.'+ index +'.fields.'+ type) == -1)? null: false;
            },
            setDefaultQuantity() {
                switch(this.data.market_object.trade_structure) {
                    case 'Outright':
                    case 'Risky':
                    case 'Fly':
                    case 'Calendar':
                        return this.data.market_object.stock ? this.quantity_default.stock
                            : this.quantity_default[this.data.market_object.market.title];
                        break;
                    case 'EFP':
                    case 'Rolls':
                        return this.quantity_default[this.data.market_object.market.title];
                        break;
                    case 'EFP Switch':
                        return this.data.market_object.markets.map( market => this.quantity_default[market.title] );
                    case 'Var Swap':
                        return 500000;
                        break;
                    default:
                        return 500;
                }
            },
            setPreviousData() {
                if(this.data.market_object.details) {
                    this.form_data.fields.forEach( (field,index) => {
                        Object.keys(field).forEach(element => {
                            if(this.data.market_object.details.fields[index].hasOwnProperty(element)) {
                                field[element] = this.data.market_object.details.fields[index][element];
                                this.chosen_option = (element == 'is_selected' && this.data.market_object.details.fields[index][element])? index : this.chosen_option;
                            }
                        });
                    });
                }
            },
        },
        created() {
    		let size_default =  this.setDefaultQuantity();
            this.display.has_strike = true;
            this.display.versus = false;
            this.display.is_ratio = false;
            this.display.is_vega = false;
            this.display.has_capped = false;
            // Sets up the view and object data defaults dictated by the structure
            switch(this.data.market_object.trade_structure) {
            	case 'Outright':
                    this.form_data.fields.push({is_selected:true,strike: null,quantity: size_default, quantity_default: size_default
                    });
            		this.display.disable_choice = true,
            		this.chosen_option = null;
            		break;
            	case 'Risky':
                    this.form_data.fields.push({is_selected:true,strike: null,quantity: size_default, quantity_default: size_default
                    });
            		this.form_data.fields.push({is_selected:false,strike: null,quantity: size_default, quantity_default: size_default
                    });
                    this.display.is_ratio = true;
            		this.chosen_option = 0;
            		break;
            	case 'Fly':
                    this.form_data.fields.push({is_selected:true,strike: null,quantity: size_default, quantity_default: size_default
                    });
                    this.form_data.fields.push({is_selected:false,strike: null,quantity: size_default, quantity_default: size_default
                    });
                    this.form_data.fields.push({is_selected:false,strike: null,quantity: size_default, quantity_default: size_default
                    });
            		this.display.disable_choice = true,
                    this.display.is_ratio = true;
            		this.form_data.fields[2].is_selected = true;
            		break;
            	case 'Calendar':
                    this.form_data.fields.push({is_selected:true,strike: null,quantity: size_default, quantity_default: size_default
                    });
                    this.form_data.fields.push({is_selected:false,strike: null,quantity: size_default, quantity_default: size_default
                    });
            		this.display.show_expiry = true,
                    this.display.is_ratio = true;
            		this.chosen_option = 0;
            		break;
                case 'EFP':
                case 'Rolls':
                    this.form_data.fields.push({is_selected:true,quantity: size_default, quantity_default: size_default
                    });
                    this.display.disable_choice = true,
                    this.display.has_strike = false;
                    this.chosen_option = null;
                    break;
                case 'EFP Switch':
                    this.form_data.fields.push({is_selected:true,quantity: size_default[0], quantity_default: size_default[0]
                    });
                    this.form_data.fields.push({is_selected:false,quantity: size_default[1], quantity_default: size_default[1]
                    });
                    this.display.disable_choice = false,
                    this.display.has_strike = false;
                    this.display.versus = true;
                    this.chosen_option = 0;
                    break;
                case 'Var Swap':
                    this.form_data.fields.push({
                        is_selected: true,
                        quantity: size_default,
                        quantity_default: size_default[0],
                        is_capped: true,
                        cap: this.cap_default,
                        cap_default: this.cap_default,
                    });
                    this.display.disable_choice = true,
                    this.display.has_strike = false;
                    this.display.is_vega = true;
                    this.display.has_capped = true;
                    this.chosen_option = null;
                    break;
            }
            this.setPreviousData();
        }
    }
</script>