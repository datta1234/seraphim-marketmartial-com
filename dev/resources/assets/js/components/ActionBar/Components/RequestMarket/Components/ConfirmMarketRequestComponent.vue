<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
            <b-row align-h="start">
                <b-col cols="3" class="mt-2">
                    <p>{{ data.market_type.title.toUpperCase() }}:</p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p>{{ data.index_market_object.market.title }}</p>
                </b-col>
            </b-row>
            <b-row align-h="start">
                <b-col cols="3" class="mt-2">
                    <p>EXPIRY:</p>
                </b-col>
                <b-col v-for="expiry_date in data.index_market_object.expiry_dates"  cols="3" class="mt-2">
                    <p>{{ castToMoment(expiry_date) }}</p>
                </b-col>
            </b-row>
            <b-row align-h="start">
                <b-col cols="3" class="mt-2">
                    <p>STRIKE:</p>
                </b-col>
                <b-col v-for="field in data.index_market_object.details.fields" cols="3" class="mt-2">
                	<p>{{ field.strike }}<span v-if="field.is_selected && data.index_market_object.details.fields.length > 1"> (CH)</span></p>
                </b-col>
            </b-row>
            <b-row align-h="start">
                <b-col cols="3" class="mt-2">
                    <p>QUANTITY:</p>
                </b-col>
                <b-col v-for="field in data.index_market_object.details.fields" cols="3" class="mt-2">
                	<p>{{ field.quantity }}</p>
                </b-col>
            </b-row>
            <b-row align-h="start">
                <b-col cols="3" class="mt-2">
                    <p>STRUCTURE:</p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p>{{ data.index_market_object.trade_structure }}</p>
                </b-col>
            </b-row>
            <b-row v-if="errors.messages.length > 0" class="text-center mt-3">
                <b-col v-for="error in errors.messages" cols="12">
                    <p class="text-danger mb-0">{{ error }}</p>
                </b-col>
            </b-row>
            <b-row align-h="center">
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