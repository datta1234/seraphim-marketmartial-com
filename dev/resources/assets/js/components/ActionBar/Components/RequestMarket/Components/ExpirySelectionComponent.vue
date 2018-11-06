<template>
    <div dusk="expiry-selection" class="expiry-selections">
        <b-container fluid>
            <mm-loader theme="light" :default_state="true" event_name="requestDatesLoaded" width="200" height="200"></mm-loader>
            <b-row v-if="dates_loaded && data.number_of_dates > 1" class="justify-content-md-center">
                <b-col class="mt-0 text-center" cols="12">
                    <p class="modal-info-text">*Select two dates to continue</p>
                </b-col>
            </b-row>
            <div v-if="dates_loaded && expiry_dates.length > 0" class="card-columns">
                <div :key="index" v-for="(expiry_date, index) in expiry_dates" class="card card-reset">
                    <b-button :disabled="checkPastDate(expiry_date.expiration_date)" class="mm-modal-market-button w-100" @click="selectExpiryDates(expiry_date.expiration_date)">
                        {{ castToMoment(expiry_date.expiration_date) }}
                    </b-button>
                </div>
            </div>
            <b-row v-if="duplicate_date" class="text-center mt-3">
                <b-col cols="12">
                    <p class="text-danger mb-0">Cannot select duplicate dates.</p>
                </b-col>
            </b-row>
            <b-row v-if="dates_loaded && expiry_dates.length > 0" class="justify-content-md-center">
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
                <b-col :key="index" v-for="(error, index) in errors.messages" cols="12">
                    <p class="text-danger mb-0">{{ error }}</p>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
    import { EventBus } from '../../../../../lib/EventBus.js';
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
                duplicate_date: false,
                dates_loaded: false,
            };
        },
        methods: {
            /**
             * Passes selected expiry date then sorts the dates and calls the component
             *  callback when the number of selected dates reach the total number of dates.
             *
             * @param {string} date
             */
            selectExpiryDates(date) {
                this.duplicate_date = this.selected_dates.indexOf(date) == -1 ? false : true;
                if(!this.duplicate_date) {
                    this.selected_dates.push(date);
                }
                if(this.selected_dates.length == this.data.number_of_dates) {
                    this.$root.dateStringArraySort(this.selected_dates, 'YYYY-MM-DD HH:mm:ss');
                    /*this.data.index_market_object.expiry_dates = this.selected_dates;*/
                    
                    this.callback(this.selected_dates.map( (current) => {
                        return this.castToMoment(current);
                    }).join(' / '), this.selected_dates);
                }
            },
            /**
             * Casting a passed string to moment with a new format
             *
             * @param {string} date_string
             */
            castToMoment(date_string) {
                return moment(date_string, 'YYYY-MM-DD HH:mm:ss').format('MMMYY'); //return to 
            },
            checkPastDate(date_string) {
                return moment(date_string).isBefore();
            },
            /**
             * Handles dates pagination
             *
             * @param {Event} $event
             */
            changePage($event) {
                EventBus.$emit('loading', 'requestDates');
                this.dates_loaded = false;
                this.current_page = $event;
                this.loadExpiryDate();    
            },
            /**
             * Loads Expiry Dates from API and sets pagination variables
             */
            loadExpiryDate() {
                axios.get(axios.defaults.baseUrl + '/trade/safex-expiration-date?page='+this.current_page)
                .then(expiryDateResponse => {
                    if(expiryDateResponse.status == 200) {
                        this.current_page = expiryDateResponse.data.current_page;
                        this.per_page = expiryDateResponse.data.per_page;
                        this.total = expiryDateResponse.data.total;
                        this.expiry_dates = expiryDateResponse.data.data;
                        EventBus.$emit('loading', 'requestDates');
                        this.dates_loaded = true;
                    } else {
                        console.error(err);    
                    }
                }, err => {
                    console.error(err);
                });
            },
        },
        mounted() {
            this.dates_loaded = false;
            this.loadExpiryDate();
        }
    }
</script>