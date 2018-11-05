<template>
    <div dusk="confirm-market-request" class="step-selections">
        <b-container fluid>
            <b-row :key="index" v-for="(display_field, index) in display_fields" align-h="start">
                <b-col cols="4">
                    <p>{{ display_field }}:</p>
                </b-col>
                <b-col cols="4">
                    <p>{{ getDisplayDetal(display_field, 0) }}</p>
                </b-col>
                <b-col cols="4">
                    <p>{{ getDisplayDetal(display_field, 1) }}</p>
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
        name: 'SwitchConfirmMarketRequest',
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
                    "UNDERLYING",
                    "EXPIRY",
                    "STRIKE",
                    "QUANTITY/RATIO",
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
            getDisplayDetal(display_field, option_index) {
                let switch_option = this.data.market_object.switch_options[option_index];
                switch (display_field) {
                    case "UNDERLYING":
                        return switch_option.is_index ? switch_option.index_market.title : switch_option.stock_selection.code;
                        break;
                    case "EXPIRY":
                            return this.castToMoment(switch_option.expiration);
                        break;
                    case "STRIKE":
                        return (switch_option.is_index ? '' : 'R') + this.$root.splitValHelper(switch_option.strike,' ',3) 
                            + (switch_option.is_selected ? '(CH)' : '');
                        break;
                    case "QUANTITY/RATIO":
                        return switch_option.is_index ? switch_option.quantity + " contracts" 
                            : this.$root.formatRandQty(switch_option.quantity) + 'm';
                        break;
                    case "STRUCTURE":
                        return option_index == 0 ? this.data.market_object.trade_structure : '';
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
        }
    }
</script>