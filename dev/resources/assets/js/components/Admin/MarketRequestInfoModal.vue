<template>
    <b-modal v-model="modal_open" centered class="mm-modal mm-modal-dark" size="lg" :no-close-on-backdrop="true" :no-close-on-esc="true">
        <template slot="modal-header">
            <h5 class="modal-title">
                <span class="mm-modal-title">Market Request Information</span>
            </h5>
        </template>
        <template v-if="loading">
            <b-container fluid>
                <p class="text-center">Loading...</p>
            </b-container>
        </template>
        <template v-else>
            <b-container fluid>
                <b-row>
                    <b-col>
                        <h4>Market Request Quotes:</h4>
                    </b-col>
                </b-row>
                <b-row v-for="(item, index) in quotes" :key="item.id" :class="{ 'text-bold market-text': true, 'is-interest': item.org_interest, 'is-maker': item.org_maker }">
                    <b-col cols="3" class="text-center" v-b-popover.hover.top="item.user">
                        {{ item.org }}
                    </b-col>
                    <b-col cols="1" class="text-center">
                         {{ item.bid_qty ? item.bid_qty : "-"  }}
                    </b-col>
                    <b-col cols="1" class="text-center">
                        {{ item.bid ? item.bid : "-"  }}
                    </b-col>
                    <b-col cols="1" class="text-center">
                        {{ item.offer ? item.offer : "-"  }}
                    </b-col>
                    <b-col cols="1" class="text-center">
                         {{ item.offer_qty ? item.offer_qty : "-"  }}
                    </b-col>
                    <b-col cols="4" class="text-center">
                        {{ getState(item) }}
                    </b-col>
                    <b-col cols="1" class="text-center">
                        <small>{{ item.time }}</small>
                    </b-col>
                </b-row>
            </b-container>
        </template>
        <template slot="modal-footer">
            <b-btn size="md" variant="default" class="pull-right" @click="closeModal()">Close</b-btn>
        </template>
    </b-modal>
</template>
<script>
    import UserMarketRequest from '~/lib/UserMarketRequest';
    import UserMarketQuote from '~/lib/UserMarketQuote';
    export default {
        props: {
            marketRequest: UserMarketRequest
        },
        data() {
            return {
                modal_open: false,
                loading: false,
                quotes: [],
            }
        },
        computed: {
            spread_message: function() {
                let ts = this.marketRequest.trade_structure_slug;
                if(['efp', 'efp_switch', 'rolls'].indexOf(ts) > -1) {
                    return "POINT SPREAD";
                }
                return "VOL SPREAD";
            }
        },
        methods: {
            getState(item) {
                if(item.bid_only) {
                    return "BID ONLY";
                }
                if(item.offer_only) {
                    return "OFFER ONLY";
                }
                if(item.vol_spread != null) {
                    return item.vol_spread+' '+this.spread_message;
                }
                return "";
            },
            showModal() {
                this.loading = false;
                this.modal_open = true;
                this.loadMarketRequestInfo();
            },
            closeModal() {
                this.modal_open = false;
                this.loading = false;
            },
            loadMarketRequestInfo() {
                this.loading = true;
                axios.get(axios.defaults.baseUrl + "/trade/admin/market-request/" + this.marketRequest.id)
                .then(response => {
                    // test if the quotes are present and there are some
                    if(response.data && response.data.quotes) {
                        this.quotes = response.data.quotes.map(x => new UserMarketQuote(x));
                    }
                    this.loading = false;
                })
                .catch(err => {
                    this.$toasted.error("An error occured loading the requested information");
                    this.loading = false;
                });
            }
        },
        mounted() {

        }
    }
</script>