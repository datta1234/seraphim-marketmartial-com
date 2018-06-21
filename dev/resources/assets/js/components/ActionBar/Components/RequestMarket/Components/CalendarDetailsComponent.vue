<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
            <b-row class="justify-content-md-center">
                <b-col cols="12">
                    <b-form @submit="submitDetails">
                        <b-row>
                            <b-col cols="3" offset="3">
                                <p><strong>{{ castToMoment(data.index_market_object.expiry_dates) }}</strong></p>
                            </b-col>
                            <b-col cols="3">
                                <p><strong>{{ castToMoment(data.index_market_object.expiry_dates) }}</strong></p>
                            </b-col>
                        </b-row>

                        <b-row>
                            <b-form-radio-group id="risky-choices" v-model="chosen_option" name="choice">
                                    <b-form-radio value="0">CHOICE</b-form-radio>
                                    <b-form-radio value="1">CHOICE</b-form-radio>
                            </b-form-radio-group>
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
        watch: {
            'chosen_option': function(chosen_index) {
                this.form_data.options.forEach( (element, index) => {
                    element.is_selected = (chosen_index == index) ? true : false;
                });
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
            castToMoment(date_string) {
                return moment(date_string, 'YYYY-MM-DD HH:mm:ss').format('MMMYY');
            },
        },
        mounted() {
            
        }
    }
</script>