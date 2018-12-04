<template>
    <b-row dusk="ibar-trade-request" v-bind:id="dynamicId">
    <b-col cols="10" >

        <b-row>
            <b-col cols="3" class="text-center">
                {{ marketNegotiation.bid_qty ? marketNegotiation.bid_qty : "-"  }}
            </b-col>

            <b-col  cols="3" class="text-center" :class="getStateClass('bid')">

                <span v-if="selectable && marketNegotiation.bid && bid_selectable" class="pointer" @click="selectOption(false)" :id="'popover-hit-'+marketNegotiation.id">
                    {{ marketNegotiation.bid ? marketNegotiation.bid_display : "-"  }}
                </span>
                <span v-else>
                    {{ marketNegotiation.bid ? marketNegotiation.bid_display : "-"  }}
                </span>

            </b-col>

            <b-col cols="3" class="text-center" :class="getStateClass('offer')">
                <span v-if="selectable && marketNegotiation.offer && offer_selectable" class="pointer" @click="selectOption(true)" :id="'popover-lift-'+marketNegotiation.id">
                    {{ marketNegotiation.offer ? marketNegotiation.offer_display : "-"  }}
                </span>
                <span v-else>
                    {{ marketNegotiation.offer ? marketNegotiation.offer_display : "-"  }}
                </span>
            </b-col>

            <b-col cols="3" class="text-center">
                {{ marketNegotiation.offer_qty ? marketNegotiation.offer_qty : "-"  }}
            </b-col>
        </b-row>
        <b-row> 

            <b-col class="condition text-center" cols="6"> <small>{{ getConditionState(marketNegotiation, "bid") }} </small></b-col>

            <b-col class="condition text-center" cols="6"> <small>{{ getConditionState(marketNegotiation, "offer") }} </small></b-col>


        </b-row>
    </b-col>
    <b-col cols="2">
        <p class="text-center">
            <small>{{ marketNegotiation.time }}</small>
        </p>
    </b-col>
    <ibar-trade-desired-quantity v-if="selectable" ref="popoverHit" :target="'popover-hit-'+marketNegotiation.id" :market-negotiation="marketNegotiation" :open="hitOpen" :is-offer="false" @close="cancelOption(false)"  parent="last-negotiation"></ibar-trade-desired-quantity>

    <ibar-trade-desired-quantity v-if="selectable" ref="popoverLift" :target="'popover-lift-'+marketNegotiation.id" :market-negotiation="marketNegotiation" :open="liftOpen" :is-offer="true" @close="cancelOption(true)"  parent="last-negotiation"></ibar-trade-desired-quantity>

    <b-col cols="12">

        <template v-if="lastTradeNegotiation != null && !lastTradeNegotiation.traded">
            <div v-for="(tradeNegotiation,index) in marketNegotiation.trade_negotiations">
                <template v-if="(tradeNegotiation.sent_by_me || tradeNegotiation.sent_to_me) 
                                && index == (marketNegotiation.trade_negotiations.length - 1)">
                    <div v-if="tradeNegotiation.getTradingText() !== null">
                        {{ tradeNegotiation.getTradingText() }} 
                    </div>
                    <ul class="text-my-org">
                        <li>{{ tradeNegotiation.getSizeText()+" "+tradeNegotiation.quantity }}</li>
                    </ul>
                </template>
                <div v-else-if="tradeNegotiation.getTradingText() !== null" class="text-my-org text-center">
                    <div v-if="tradeNegotiation.getTradingText() !== null">
                        {{ tradeNegotiation.getTradingText() }} 
                    </div>
                </div>
            </div>
        </template>
        <div v-else-if="lastTradeNegotiation != null && lastTradeNegotiation.traded" class="text-my-org text-center">
            <div v-for="(tradeNegotiation,index) in marketNegotiation.trade_negotiations">
                {{ tradeNegotiation.getTradingText() }}
            </div>
        </div>
    </b-col> 
    
    <b-col v-if="isCurrent && lastTradeNegotiation != null && lastTradeNegotiation.traded">
        <b-row dusk="ibar-trade-request-open">
            <b-col cols="10">
                <b-row>
                    <b-col cols="3" class="text-center">
                        -
                    </b-col>
                    <b-col  cols="3" class="text-center">
                        -
                    </b-col>
                    <b-col cols="3" class="text-center">
                        -
                    </b-col>
                    <b-col cols="3" class="text-center">
                        -
                    </b-col>
                </b-row>
            </b-col>
            <b-col cols="2">
                <p class="text-center">
                    <small></small>
                </p>
            </b-col>
        </b-row>
    </b-col>
</b-row>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    export default {
        name: 'ibar-trade-request',
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation,
                default: null
            },
            selectable: {
                type: Boolean,
                default: null
            },
            isCurrent: Boolean
        },
        data() {
            return {
                conditionAttr:[],
                dialogText: '',
                liftOpen: false,
                hitOpen: false
            };
       },
       watch: {

       },
       computed: {
        dynamicId: function(){
            return this.selectable ? "last-negotiation" : "userMarket-Negotiation-level-"+ this.marketNegotiation.id; 
        },
        lastTradeNegotiation: function(){
            return this.marketNegotiation.getLastTradeNegotiation();
        },
        firstTradeNegotiation: function(){
            return this.marketNegotiation.getFirstTradeNegotiation();
        },
        canBid: function(){
            let source = this.marketNegotiation.getAmountSource("bid");
            return !source.is_my_org;
        },
        canOffer: function(){
            let source = this.marketNegotiation.getAmountSource("offer");
            return !source.is_my_org;
        },
        bid_selectable: function() {
            if(this.marketNegotiation.cond_buy_best === false) {
                return this.marketNegotiation._user_market.trading_at_best.is_my_org;
            }
            return true;   
        },
        offer_selectable: function() {
            if(this.marketNegotiation.cond_buy_best === true) {
                return this.marketNegotiation._user_market.trading_at_best.is_my_org;
            }
            return true;
        },
    },
    watch: {
        'marketNegotiation': function() {
            this.liftOpen = false;
            this.hitOpen = false;
        }
    },
    methods: {
        selectOption(isOffer)
        {

        if(isOffer && this.canOffer)
        {
            this.liftOpen = true; 
            this.hitOpen = false;

        }else if(!isOffer && this.canBid)
        {
            this.liftOpen = false;
            this.hitOpen = true;
        }              
       },
       cancelOption(isOffer)
       {
           this.liftOpen = false
           this.hitOpen = false;
       },
       getConditionState(marketNegotiation, field) {

            let getConditionText = (cond, object, field) => {
                // ensure the value exists in both object and condition test
                if(typeof object[cond.condition] !== 'undefined' && typeof cond[String(object[cond.condition])] !== 'undefined') {
                    if(cond[String(object[cond.condition])].constructor === Object && typeof cond[String(object[cond.condition])].condition !== 'undefined') {
                        return getConditionText(cond[String(object[cond.condition])], object, field)
                    }
                    return cond[String(object[cond.condition])][field]
                }
                return null
            };

            for(let k in this.$root.config("condition_titles")) {
                let cond = this.$root.config("condition_titles")[k];
                let text = getConditionText(cond, marketNegotiation, field);
                let source = marketNegotiation.getAmountSource(field);
                // text exists and source of side(bid/offer) is self
                if(text != null && source.creation_idx == marketNegotiation.creation_idx) {
                    return text;
                }
            }
            return null;
        },
        getStateClass(attr) {
            console.log("market negotiation",this.marketNegotiation);

            if(this.marketNegotiation[attr] == null) {
                return "";
            }
            let source = this.marketNegotiation.getAmountSource(attr);
            return {
                "text": source[attr],
                "is-interest":source.is_interest && !source.is_my_org,
                "is-maker":source.is_maker && !source.is_my_org,
                "is-my-org":source.is_my_org
            };        
        }
    },
    mounted() {
        this.conditionAttr = Object.keys(this.$root.config("condition_titles"));
    }
 }
</script>
