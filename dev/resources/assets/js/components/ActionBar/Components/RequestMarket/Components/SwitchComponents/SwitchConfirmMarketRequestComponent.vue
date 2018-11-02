<template>
    <div dusk="confirm-market-request" class="step-selections">
        <b-container fluid>
            <b-row v-if="data.market_object.stock" align-h="start">
                <b-col cols="3" class="mt-2">
                    <p>STOCK NAME:</p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p>{{ data.market_object.stock.code }}</p>
                </b-col>
            </b-row>
            <b-row v-else align-h="start">
                <b-col cols="3" class="mt-2">
                    <p>{{ data.market_type.title.toUpperCase() }}:</p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p>{{ data.market_object.market.title }}</p>
                </b-col>
            </b-row>
            <b-row align-h="start">
                <b-col cols="3" class="mt-2">
                    <p>EXPIRY:</p>
                </b-col>
                <b-col :key="index" v-for="(expiry_date, index) in data.market_object.expiry_dates"  cols="3" class="mt-2">
                    <p>{{ castToMoment(expiry_date) }}</p>
                </b-col>
            </b-row>
            <b-row align-h="start">
                <b-col cols="3" class="mt-2">
                    <p>STRIKE:</p>
                </b-col>
                <b-col :key="index" v-for="(field, index) in data.market_object.details.fields" cols="3" class="mt-2">
                	<p>{{ (data.market_object.stock ? "R" : "") + splitValHelper(field.strike,' ',3) }}<span v-if="field.is_selected && data.market_object.details.fields.length > 1"> (CH)</span></p>
                </b-col>
            </b-row>
            <b-row align-h="start">
                <b-col cols="3" class="mt-2">
                    <p>QUANTITY:</p>
                </b-col>
                <b-col :key="index" v-for="(field, index) in data.market_object.details.fields" cols="3" class="mt-2">
                	<p>{{ data.market_object.stock ? formatRandQty(field.quantity) + 'm' 
                        : splitValHelper(field.quantity,' ',3) }}</p>
                </b-col>
            </b-row>
            <b-row align-h="start">
                <b-col cols="3" class="mt-2">
                    <p>STRUCTURE:</p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p>{{ data.market_object.trade_structure }}</p>
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
        name: 'ConfirmMarketRequest',
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
            };
        },
        methods: {
            /**
             * Calls the component call back method
             */
            confirmDetails() {
                this.callback();
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
        }
    }
</script>