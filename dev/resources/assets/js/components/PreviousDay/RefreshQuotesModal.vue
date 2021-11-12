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
                <b-row class="mb-3" v-if="quotes.length > 0">
                    <b-col cols="12">
                        <label>Quotes:</label>
                    </b-col>
                    <b-col cols="11" offset="1">
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
                    <b-col cols="12" v-if="negotiations.length > 0">
                        <b-btn size="md" variant="primary" class="float-right" v-active-request @click="refresh(true, false)">Refresh Quotes Only</b-btn>
                    </b-col>
                </b-row>
                <b-row v-if="negotiations.length > 0" class="mb-3">
                    <b-col cols="12">
                        <label>Levels:</label>
                    </b-col>
                    <b-col cols="11" offset="1">
                        <b-row v-for="(negotiation, index) in negotiations" :key="index">
                            <b-col>
                                {{ negotiation.market_request_summary }}
                            </b-col>
                            <b-col>
                                <b-row>
                                    <b-col cols="3" class="text-center">
                                        ({{ negotiation.bid_qty ? negotiation.bid_qty : '&nbsp;-&nbsp;' }})&nbsp;&nbsp;<span v-bind:class="{ 'user-action-dark': negotiation.owns_bid }">{{ negotiation.bid ? negotiation.bid : '&nbsp;-&nbsp;' }}</span>&nbsp;/&nbsp;<span v-bind:class="{ 'user-action-dark': negotiation.owns_offer }">{{ negotiation.offer ? negotiation.offer : '&nbsp;-&nbsp;' }}</span>&nbsp;&nbsp;({{ negotiation.offer_qty ? negotiation.offer_qty : '&nbsp;-&nbsp;' }})
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col>
                                <b-form-checkbox v-model="selected_negotiations" :value="negotiation.id">
                                    Refresh
                                </b-form-checkbox>
                            </b-col>
                        </b-row>
                    </b-col>
                    <b-col cols="12" v-if="quotes.length > 0">
                        <b-btn size="md" variant="primary" class="float-right" :disabled="disabled_submit" v-active-request @click="refresh(false, true)">Refresh Levels Only</b-btn>
                    </b-col>
                </b-row>
                <b-row>
                    <p>
                        <strong>WARNING: The quotes / levels not selected for refresh will be killed.</strong>
                    </p>
                </b-row>
            </b-container>
        </template>
        <template slot="modal-footer">
            <b-btn size="md" variant="primary" class="float-right" :disabled="disabled_submit" v-active-request @click="refresh(true, true)">Refresh</b-btn>
            <b-btn size="md" variant="default" class="float-right" @click="closeModal()">Close</b-btn>
        </template>
    </b-modal>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import UserMarketQuote from '~/lib/UserMarketQuote';
    export default {
        data() {
            return {
                modal_open: false,
                quotes: [],
                negotiations: [],
                selected: [],
                selected_negotiations: [],
            }
        },
        computed: {
            disabled_submit() {

                return !(this.selected.length > 0 || this.selected_negotiations.length > 0);
            },
        },
        methods: {
            closeModal() {
                let res = confirm("Are you sure you wish to take no action?");
                if(res) {
                    this.modal_open = false;
                }
            },
            refresh(quotes, levels) {
                let promises = [];
                if(quotes && this.quotes.length > 0) {
                    promises.push(this.refreshQuotes());
                }
                if(levels && this.negotiations.length > 0) {
                    promises.push(this.refreshNegotiations());
                }
                Promise.all(promises).then((data) => {
                    data = data.reduce((a,v) => a && v, true);
                    if(data) {
                        this.modal_open = false;
                    }
                })
            },
            refreshQuotes() {
                if(this.quotes.length == 0) {
                    return;
                }
                let res = true;
                if(this.selected.length == 0) {
                    res = confirm("You are choosing NOT to refresh any of your quotes from yesterday.\nNone of these quotes will be actionable by the interest. \n\nConfirm cancellation of quotes?");
                }
                if(res) {
                    return axios.post(axios.defaults.baseUrl + "/trade/previous-quotes", {
                        refresh: this.selected
                    })
                    .then(response => {
                        if(response.data.success) {
                            this.$toasted.success(response.data.data.message);
                        } else {
                            this.$toasted.error("An error occured refreshing your quotes, contact an administrator if the problem presists");
                        }
                        return response.data.success;
                    })
                    .catch(err => {
                        this.$toasted.error("An error occured refreshing your quotes, contact an administrator if the problem presists");
                    });
                } else {
                    return false;
                }
            },
            refreshNegotiations() {
                if(this.negotiations.length == 0) {
                    return;
                }
                let res = true;
                if(this.selected_negotiations.length == 0) {
                    res = confirm("You are choosing NOT to refresh any of your levels from yesterday.\nNone of these levels will be actionable by the interest. \n\nConfirm cancellation of levels?");
                }
                if(res) {
                    return axios.post(axios.defaults.baseUrl + "/trade/previous-negotiations", {
                        refresh: this.selected_negotiations
                    })
                    .then(response => {
                        if(response.data.success) {
                            this.$toasted.success(response.data.data.message);
                        } else {
                            this.$toasted.error("An error occured refreshing your levels, contact an administrator if the problem presists");
                        }
                        return response.data.success;
                    })
                    .catch(err => {
                        this.$toasted.error("An error occured refreshing your levels, contact an administrator if the problem presists");
                    });
                } else {
                    return false;
                }
            },
            loadOldQuotes() {
                return axios.get(axios.defaults.baseUrl + "/trade/previous-quotes")
                .then(response => {
                    // test if the quotes are present and there are some
                    if(response.data && response.data.data && response.data.data.constructor == Array) {
                        this.quotes = response.data.data.map(x => new UserMarketQuote(x));
                    }
                    return this.quotes;
                })
                .catch(err => {
                    this.$toasted.error("An error occured loading the previous days quotes, contact an administrator if the problem presists");
                });
            },
            loadOldNegotiations() {
                return axios.get(axios.defaults.baseUrl + "/trade/previous-negotiations")
                .then(response => {
                    // test if the negotiations are present and there are some
                    if(response.data && response.data.data && response.data.data.constructor == Array) {
                        this.negotiations = response.data.data.map(x => new UserMarketNegotiation(x));
                    }
                    return this.negotiations;
                })
                .catch(err => {
                    this.$toasted.error("An error occured loading the previous days levels, contact an administrator if the problem presists");
                });
            }
        },
        mounted() {
            if(!this.$root.is_admin && !this.$root.is_viewer) {
                Promise.all([
                    this.loadOldQuotes(), 
                    this.loadOldNegotiations()
                ])
                .then((data) => {
                    if(data[0].length > 0 || data[1].length > 0) {
                        this.modal_open = true;
                    }
                });
            }
        }
    }
</script>