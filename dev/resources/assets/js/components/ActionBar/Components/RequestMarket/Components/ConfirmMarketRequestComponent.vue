<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
        	<b-row class="justify-content-md-center">
                <b-col cols="3" class="mt-2">
                    <p>INDEX:</p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p>{{ data.index_market_object.market }}</p>
                </b-col>
            </b-row>
            <b-row class="justify-content-md-center">
                <b-col cols="3" class="mt-2">
                    <p>EXPIRY:</p>
                </b-col>
                <b-col v-for="expiry_date in data.index_market_object.expiry_dates"  cols="3" class="mt-2">
                    <p>{{ castToMoment(expiry_date) }}</p>
                </b-col>
            </b-row>
            <b-row class="justify-content-md-center">
                <b-col cols="3" class="mt-2">
                    <p>STRIKE:</p>
                </b-col>
                <b-col v-for="option in data.index_market_object.details.options" cols="3" class="mt-2">
                	<p>{{ option.strike }}<span v-if="option.is_selected"> (CH)</span></p>
                </b-col>
            </b-row>
            <b-row class="justify-content-md-center">
                <b-col cols="3" class="mt-2">
                    <p>QUANTITY:</p>
                </b-col>
                <b-col v-for="option in data.index_market_object.details.options" cols="3" class="mt-2">
                	<p>{{ option.quantity }}</p>
                </b-col>
            </b-row>
            <b-row class="justify-content-md-center">
                <b-col cols="3" class="mt-2">
                    <p>STRUCTURE:</p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p>{{ data.index_market_object.trade_structure }}</p>
                </b-col>
            </b-row>
            <b-row class="justify-content-md-center">
                <b-col cols="6" class="mt-2">
                    <b-button class="mm-modal-market-button-alt w-100" @click="confirmDetails()">
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
            }
        },
        data() {
            return {
            };
        },
        methods: {
            confirmDetails() {
                this.callback();
            },
            castToMoment(date_string) {
                return moment(date_string, 'YYYY-MM-DD HH:mm:ss').format('MMMYY');
            },
        },
        mounted() {
            console.log("FINAL STEP",this.data);
        }
    }
</script>