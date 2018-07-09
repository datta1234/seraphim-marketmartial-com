<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
            <b-row v-if="data.number_of_dates > 1" class="justify-content-md-center">
                <b-col class="mt-0 text-center" cols="12">
                    <p class="modal-info-text">*Calendar structure requires {{ data.number_of_dates }} expiry dates. Second date selected will continue your market request process</p>
                </b-col>
            </b-row>
            <div v-if="expiry_dates.length > 0" class="card-columns">
                <div v-for="expiry_date in expiry_dates" class="card card-reset">
                    <b-button :disabled="checkPastDate(expiry_date.expiration_date)" class="mm-modal-market-button w-100" @click="selectExpiryDates(expiry_date.expiration_date)">
                        {{ castToMoment(expiry_date.expiration_date) }}
                    </b-button>
                </div>
            </div>
            <b-row v-if="expiry_dates.length > 0" class="justify-content-md-center">
                <b-col cols="12" class="mt-5">
                    <b-pagination @change="changePage($event)" 
                                  align="center" 
                                  :total-rows="total"
                                  :hide-ellipsis="true"
                                  v-model="current_page" 
                                  :per-page="per_page">
                    </b-pagination>
                </b-col>
            </b-row>
            <b-row v-if="errors.messages.length > 0" class="text-center mt-3">
                <b-col v-for="error in errors.messages" cols="12">
                    <p class="text-danger mb-0">{{ error }}</p>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
    export default {
        name: 'ExpirySelection',
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
        data() {
            return {
                expiry_dates:[],
                current_page:1,
                per_page:12,
                total:null,
                selected_dates:[],
            };
        },
        methods: {
            selectExpiryDates(date) {
                this.selected_dates.push(date);
                if(this.selected_dates.length == this.data.number_of_dates) {    
                    this.data.index_market_object.expiry_dates = this.selected_dates;
                    this.callback();//add title dates
                }
            },
            castToMoment(date_string) {
                return moment(date_string, 'YYYY-MM-DD HH:mm:ss').format('MMMYY'); //return to 
            },
            checkPastDate(date_string) {
                return moment(date_string).isBefore();
            },
            changePage($event) {
                this.current_page = $event;
                this.loadExpiryDate();    
            },
            /**
             * Loads Expiry Dates
             */
            loadExpiryDate() {
                axios.get(axios.defaults.baseUrl + '/trade/safex-expiration-date?page='+this.current_page)
                .then(expiryDateResponse => {
                    if(expiryDateResponse.status == 200) {
                        this.current_page = expiryDateResponse.data.current_page;
                        this.per_page = expiryDateResponse.data.per_page;
                        this.total = expiryDateResponse.data.total;
                        this.expiry_dates = expiryDateResponse.data.data;
                    } else {
                        console.error(err);    
                    }
                }, err => {
                    console.error(err);
                });
            },
        },
        mounted() {
            this.loadExpiryDate();
        }
    }
</script>