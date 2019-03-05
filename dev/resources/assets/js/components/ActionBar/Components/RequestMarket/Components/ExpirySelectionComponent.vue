<template>
    <div dusk="expiry-selection" class="expiry-selections">
        <b-container fluid>
            <mm-loader theme="light" :default_state="true" event_name="requestDatesLoaded" width="200" height="200"></mm-loader>
            <b-row v-if="dates_loaded && data.number_of_dates > 1" class="justify-content-md-center">
                <b-col class="mt-0 text-center" cols="12">
                    <p class="modal-info-text">*Select two dates to continue</p>
                </b-col>
            </b-row>
            <b-row v-if="dates_loaded">
                <b-col :key="year" v-for="(expiry_date_arr, year) in expiry_dates" cols="4">
                    <b-row :key="index" v-for="(expiry_date, index) in expiry_date_arr" class="mt-2">
                        <b-col>
                            <b-button :disabled="checkPastDate(expiry_date)" 
                                      class="mm-modal-market-button-toggle w-100"
                                      v-bind:class="{'selected': selected_dates.indexOf(expiry_date) > -1}"
                                      @click="selectExpiryDates(expiry_date)">
                                {{ castToMoment(expiry_date) }}
                            </b-button>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
            <b-row v-if="dates_loaded" class="justify-content-md-center">
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

            <b-row v-if="this.selected_dates.length == this.data.number_of_dates" class="justify-content-md-center mt-3">
                <b-col cols="6">
                    <b-button class="mm-modal-market-button-alt w-100" @click="submitDetails()">
                        Next
                    </b-button>
                </b-col>
            </b-row>

        </b-container>
    </div>
</template>

<script>
    import { EventBus } from '~/lib/EventBus.js';
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
                dates_loaded: false,
            };
        },
        methods: {
            /**
             * Adds or removes chosen date based on wether it has been selected already
             *  then calls submitDetails.
             *
             * @param {string} date
             */
            selectExpiryDates(date) {
                let date_index = this.selected_dates.indexOf(date);

                // Checks if the date is already selected
                if(date_index > -1) {
                    // Removes date if already selected
                    this.selected_dates.splice(date_index, 1);
                } else {
                    // Checks if max number of dates already present
                    if(this.selected_dates.length == this.data.number_of_dates) {
                        this.selected_dates.pop();
                    }
                    this.selected_dates.push(date);
                }

                this.submitDetails();
            },
            /**
             * Sorts the dates and calls the component
             *  callback when the number of selected dates reach the total number of dates.
             */
            submitDetails() {
                if(this.selected_dates.length == this.data.number_of_dates) {
                    this.$root.dateStringArraySort(this.selected_dates, 'YYYY-MM-DD HH:mm:ss');
                    /*this.data.index_market_object.expiry_dates = this.selected_dates;*/
                    
                    this.callback(this.selected_dates.map( (current) => {
                        return this.castToMoment(current);
                    }).join( this.data.market_object.trade_structure == 'Rolls' ? ' vs ' : ' / ' ), this.selected_dates);
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
                    this.current_page = expiryDateResponse.data.current_page;
                    this.per_page = expiryDateResponse.data.per_page;
                    this.total = expiryDateResponse.data.total;
                    this.filterDates(expiryDateResponse.data.data);
                    EventBus.$emit('loading', 'requestDates');
                    this.dates_loaded = true;
                }, err => {
                    //console.error(err);
                    this.$toasted.error("Failed to load safex expiration dates");
                });
            },
            /**
             * Filters passed array of dates to arrays orderd by years. 
             *
             * @param {Array} $expiry_dates
             */
            filterDates(expiry_dates) {
                this.expiry_dates = {};
                expiry_dates.forEach(expiry_date => {
                    let year = moment(expiry_date.expiration_date, 'YYYY-MM-DD HH:mm:ss').format('YYYY');
                    if(typeof this.expiry_dates[year] === "undefined") {
                        this.expiry_dates[year] = [];
                    }
                    this.expiry_dates[year].push(expiry_date.expiration_date);
                });
            },
        },
        mounted() {
            this.dates_loaded = false;
            this.loadExpiryDate();
            if(this.data.market_object.expiry_dates.length <= this.data.number_of_dates && this.data.market_object && this.data.market_object.expiry_dates) {
                this.selected_dates = this.data.market_object.expiry_dates;
            }
        }
    }
</script>