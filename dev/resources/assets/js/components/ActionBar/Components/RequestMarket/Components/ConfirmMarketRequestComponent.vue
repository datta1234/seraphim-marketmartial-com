<template>
    <div dusk="confirm-market-request" class="step-selections">
        <b-container fluid>
            <b-row v-if="display.is_versus_market" align-h="start" class="mt-0 mb-0">
                <b-col cols="3" class="mt-2">
                    <p class="m-0">INDEX:</p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p class="m-0">
                        {{ data.market_object.markets[0].title }}
                        <span v-if="data.market_object.details.fields[0].is_selected && display.show_choice"> (CH)</span>
                    </p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p class="m-0">VS.</p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p class="m-0">
                        {{ data.market_object.markets[1].title }}
                        <span v-if="data.market_object.details.fields[1].is_selected && display.show_choice"> (CH)</span>
                    </p>
                </b-col>
            </b-row>
            <b-row v-else-if="display.is_stock_only" align-h="start" class="mt-0 mb-0">
                <b-col cols="3" class="mt-2">
                    <p class="m-0">STOCK NAME:</p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p class="m-0">{{ data.market_object.stock.code }}</p>
                </b-col>
            </b-row>
            <b-row v-else align-h="start" class="mt-0 mb-0">
                <b-col cols="3" class="mt-2">
                    <p class="m-0">{{ data.market_type.title.toUpperCase() }}:</p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p class="m-0">{{ data.market_object.market.title }}</p>
                </b-col>
            </b-row>
            <b-row align-h="start" class="mt-0 mb-0">
                <b-col cols="3" class="mt-2">
                    <p class="m-0">EXPIRY:</p>
                </b-col>
                <b-col v-if="display.is_versus_date" cols="3" class="mt-2">
                    <p class="m-0">{{ data.market_object.expiry_dates.map(x => this.castToMoment(x)).join(' vs ') }}</p>
                </b-col>
                <b-col  v-else :key="index" v-for="(expiry_date, index) in data.market_object.expiry_dates"  
                        cols="3" 
                        class="mt-2">
                    <p class="m-0">{{ castToMoment(expiry_date) }}</p>
                </b-col>
            </b-row>
            <b-row v-if="display.has_strike" align-h="start" class="mt-0 mb-0">
                <b-col cols="3" class="mt-2">
                    <p class="m-0">STRIKE:</p>
                </b-col>
                <b-col :key="index" v-for="(field, index) in data.market_object.details.fields" cols="3" class="mt-2">
                	<p class="m-0">
                        {{ (data.market_object.stock ? "R" : "") + splitValHelper(field.strike,' ',3) }}
                        <span v-if="field.is_selected && display.show_choice"> (CH)</span>
                    </p>
                </b-col>
            </b-row>
            <b-row align-h="start" class="mt-0 mb-0">
                <b-col cols="3" class="mt-2">
                    <p class="m-0">QUANTITY:</p>
                </b-col>
                <b-col  :key="index" v-for="(field, index) in data.market_object.details.fields"
                        cols="3"
                        :offset="(display.is_versus_market && index != 0) ? 3 : 0" 
                        class="mt-2">
                	<p class="m-0">{{ data.market_object.stock ? formatRandQty(field.quantity) + 'm' 
                        : splitValHelper(field.quantity,' ',3) }}</p>
                </b-col>
            </b-row>
            <b-row align-h="start" class="mt-0 mb-0">
                <b-col cols="3" class="mt-2">
                    <p class="m-0">STRUCTURE:</p>
                </b-col>
                <b-col cols="3" class="mt-2">
                    <p class="m-0">{{ data.market_object.trade_structure }}</p>
                </b-col>
            </b-row>
            <b-row v-if="errors.messages.length > 0" class="text-center mt-3">
                <b-col :key="index" v-for="(error, index) in errors.messages" cols="12">
                    <p class="text-danger mb-0">{{ error }}</p>
                </b-col>
            </b-row>
            <b-row align-h="center">
                <b-col cols="6" class="mt-4">
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
                display:{
                    is_stock_only: false,
                    show_choice: false,
                    has_strike: true,
                    is_versus_market: false,
                    is_versus_date: false,
                },
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
        created() {
            this.display.show_choice = true;
            this.display.has_strike = true;
            this.display.is_versus_market = false;
            this.display.is_versus_date = false;

            this.display.is_stock_only = this.data.market_object.stock ? true : false;
            
            switch(this.data.market_object.trade_structure) {
                case 'Outright':
                    this.display.show_choice = false;
                    break;
                case 'Risky':
                case 'Fly':
                case 'Calendar':
                    break;
                case 'EFP':
                    this.display.has_strike = false;
                    break;
                case 'Rolls':
                    this.display.has_strike = false;
                    this.display.is_versus_date = true;
                    break;
                case 'EFP Switch':
                    this.display.has_strike = false;
                    this.display.is_versus_market = true;
                    break;
                default:

            }
        }
    }
</script>