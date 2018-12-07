<template>
    <b-modal v-model="modal_open" centered class="mm-modal" size="lg" :no-close-on-backdrop="true" :no-close-on-esc="true">
        <template slot="modal-header">
            <h5 class="modal-title">
                <span class="mm-modal-title">Your Unfinished Business</span>
            </h5>
        </template>
        <template>
            <b-container fluid>
                <b-row>
                    <p>
                        Welcome back to Market Martial. As one of yesterday's best, you have the opportunity to refresh your quotes for today. Select those you wish to refresh. Those not selected will be killed.
                    </p>
                </b-row>
                <b-row class="mb-3">
                    <b-col>
                        <b-row v-for="(quote, index) in quotes" :key="index">
                            <b-col>
                                {{ quote.market_request_summary }}
                            </b-col>
                            <b-col>
                                <b-row>
                                    <b-col cols="3" class="text-center">
                                        ({{ quote.bid_qty ? quote.bid_qty : '&nbsp;-&nbsp;' }})&nbsp;&nbsp;{{ quote.bid ? quote.bid : '&nbsp;-&nbsp;' }}&nbsp;/&nbsp;{{ quote.offer ? quote.offer : '&nbsp;-&nbsp;' }}&nbsp;&nbsp;({{ quote.offer_qty ? quote.offer_qty : '&nbsp;-&nbsp;' }})
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col>
                                <b-form-checkbox v-model="selected" :value="quote.id">
                                    Refresh
                                </b-form-checkbox>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
                <b-row>
                    <p>
                        <strong>WARNING: The quotes not selected for refresh will be killed.</strong>
                    </p>
                </b-row>
            </b-container>
        </template>
        <template slot="modal-footer">
            <b-btn size="md" variant="primary" class="pull-right" @click="refreshQuotes()">Refresh</b-btn>
            <b-btn size="md" variant="default" class="pull-right" @click="modal_open = false">Close</b-btn>
        </template>
    </b-modal>
</template>
<script>
    import UserMarketQuote from '~/lib/UserMarketQuote';
    export default {
        data() {
            return {
                modal_open: false,
                quotes: [],
                selected: [],
            }
        },
        methods: {
            refreshQuotes() {
                let res = true;
                if(this.selected.length == 0) {
                    res = confirm("You are choosing NOT to refresh any of your quotes from yesterday.\nNone of these quotes will be actionable by the interest. \n\nConfirm cancellation of quotes?");
                }
                if(res) {
                    axios.post(axios.defaults.baseUrl + "/trade/previous-quotes", {
                        refresh: this.selected
                    })
                    .then(response => {
                        if(response.data.success) {
                            this.modal_open = true;
                            this.$toasted.success(response.data.data.message);
                        } else {
                            this.$toasted.error("An error occured refreshing your quotes, contact an administrator if the problem presists");
                        }
                    })
                    .catch(err => {
                        this.$toasted.error("An error occured refreshing your quotes, contact an administrator if the problem presists");
                    });
                }
            },
            loadOldQuotes() {
                axios.get(axios.defaults.baseUrl + "/trade/previous-quotes")
                .then(response => {
                    // test if the quotes are present and there are some
                    if(response.data && response.data.constructor == Array && response.data.length > 0) {
                        this.quotes = response.data.map(x => new UserMarketQuote(x));
                        this.modal_open = true;
                    }
                })
                .catch(err => {
                    this.$toasted.error("An error occured loading the previous days quotes, contact an administrator if the problem presists");
                });
            }
        },
        mounted() {
            this.loadOldQuotes();
        }
    }
</script>