<template>
    <div dusk="alert-cleared" class="alert-cleared">
        <b-form-checkbox @change="toggleAletCleared" id="market-request-subscribe" v-model="market_request_subscribe">
            Alert me when cleared
        </b-form-checkbox>
    </div>
</template>
<script>
    export default {
        props: {
            'market_request': {
                type: Object,
            }
        },

        data() {
            return {
                market_request_subscribe: false,
            };
        },
        methods: {
            toggleAletCleared(checked) {
                axios.post(axios.defaults.baseUrl + 'trade/market-request-subscribe/' + this.market_request.id,
                    {'market_request_subscribe': checked ? "1" : "0"})
                .then(subscriptionResponse => {
                    this.$toasted.success(subscriptionResponse.data.message, { 
                         duration : 3000
                    });
                }, err => {
                    this.$toasted.error(subscriptionResponse.data.message);
                    console.error(err);
                });
            },
        },
        mounted() {
            this.market_request_subscribe = this.market_request.chosen_user_market.is_watched;
        }
    }
</script>