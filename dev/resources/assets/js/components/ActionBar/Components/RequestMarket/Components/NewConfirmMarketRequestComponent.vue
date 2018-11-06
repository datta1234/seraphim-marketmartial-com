<template>
    <div dusk="confirm-market-request" class="confirm-market-request">
        <b-container fluid>
            <b-row :key="index" v-for="(display_field, index) in display_fields" align-h="center">
                <b-col cols="2">
                    <p>{{ display_field }}:</p>
                </b-col>
                <b-col cols="4">
                    <p>{{ getDisplayDetal(display_field) }}</p>
                </b-col>
            </b-row>
            <b-row v-if="errors.messages.length > 0" class="text-center mt-3">
                <b-col :key="index" v-for="(error, index) in errors.messages" cols="12">
                    <p class="text-danger mb-0">{{ error }}</p>
                </b-col>
            </b-row>
            <b-row align-h="center">
                <b-col cols="6" class="mt-2">
                    <b-button id="confirm-request-market" class="mm-modal-market-button-alt w-100" @click="confirmDetails()">
                        Send Request
                    </b-button>
                </b-col>
            </b-row>  
        </b-container>
    </div>
</template>

<script>
    export default {
        name: 'NewConfirmMarketRequest',
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
                display_fields: [
                    "INDEX",
                    "EXPIRY",
                    "QUANTITY",
                    "STRUCTURE",
                ]
            };
        },
        methods: {
            /**
             * Calls the component call back method
             */
            confirmDetails() {
                this.callback();
            },
            getDisplayDetal(display_field) {
                switch (display_field) {
                    case "INDEX":
                        return this.data.market_object.market.title;
                        break;
                    case "EXPIRY":
                            return this.data.market_object.expiry_dates.map(x => this.castToMoment(x)).join(' vs ');
                        break;
                    case "QUANTITY":
                        return this.data.market_object.details.fields[0].quantity + " contracts";
                        break;
                    case "STRUCTURE":
                        return this.data.market_object.trade_structure;
                        break;
                    default:
                        return '-';
                }
            },
            /**
             * Casting a passed string to moment with a new format
             *
             * @param {string} date_string
             */
            castToMoment(date_string) {
                return moment(date_string, 'YYYY-MM-DD HH:mm:ss').format('MMMYY');
            },
        },
        mounted() {
            console.log("DATA: ", this.data.market_object);
        }
    }
</script>