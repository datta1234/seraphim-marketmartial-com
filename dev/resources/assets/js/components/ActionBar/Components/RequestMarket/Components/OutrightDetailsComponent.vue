<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
            <b-row class="justify-content-md-center">
                <b-form @submit="submitDetails">
                    <b-form-group horizontal label="Strike:" label-for="outright-strike">
                        <b-form-input id="outright-strike"
                                  type="number"
                                  v-model="form_data.options[0].strike"
                                  required>
                        </b-form-input>
                    </b-form-group>

                    <b-form-group horizontal label="Quantity" label-for="outright-quantity">
                        <b-form-input id="outright-quantity"
                                  type="number"
                                  v-model="form_data.options[0].quantity"
                                  placeholder="500"
                                  required>
                        </b-form-input>
                    </b-form-group>
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
        name: 'OutrightDetails',
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
                    choice: null,
                    options: [
                        {
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