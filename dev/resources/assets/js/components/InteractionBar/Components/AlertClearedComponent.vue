<template>
    <div dusk="alert-cleared" class="alert-cleared">
        <b-form-checkbox v-active-request @change="toggleAletCleared" id="market-request-subscribe" v-model="market_request.chosen_user_market.is_watched">
            Alert me when cleared
        </b-form-checkbox>
    </div>
</template>
<script>
    import { EventBus } from '~/lib/EventBus.js';
    export default {
        props: {
            'market_request': {
                type: Object,
            }
        },

        data() {
            return {
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
        }
    }
</script>