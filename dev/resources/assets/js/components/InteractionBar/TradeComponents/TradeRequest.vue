<template>
    <b-row dusk="ibar-trade-request" v-bind:id="dynamicId">
                   <b-col cols="10" >
        
                    <b-row>
                        <b-col cols="3" class="text-center">
                             {{ marketNegotiation.bid_qty ? marketNegotiation.bid_qty : "-"  }}
                        </b-col>
                        
                        <b-col  cols="3" class="text-center" :class="getStateClass('bid')">

                            <span v-if="selectable" class="pointer" @click="selectOption(false)" id="popover-hit">
                            {{ marketNegotiation.bid ? marketNegotiation.bid_display : "-"  }}
                            </span>

                            <span v-if="!selectable">
                            {{ marketNegotiation.bid ? marketNegotiation.bid_display : "-"  }}
                            </span>

                        </b-col>

                        <b-col cols="3" class="text-center" :class="getStateClass('offer')">
                            
                            <span v-if="selectable" class="pointer" @click="selectOption(true)" id="popover-lift">
                            {{ marketNegotiation.offer ? marketNegotiation.offer_display : "-"  }}
                            </span>

                            <span v-if="!selectable">
                            {{ marketNegotiation.offer ? marketNegotiation.offer_display : "-"  }}
                            </span>
                        </b-col>
                        
                        <b-col cols="3" class="text-center">
                             {{ marketNegotiation.offer_qty ? marketNegotiation.offer_qty : "-"  }}
                        </b-col>
                    </b-row>
                    <b-row> 
                        
                        <b-col class="condition text-center" cols="6"> <small>{{ getConditionState(marketNegotiation) }} </small></b-col>
                        
                        <b-col class="condition text-center" cols="6"> <small>{{ getConditionState(marketNegotiation) }} </small></b-col>


                    </b-row>
                </b-col>
                <b-col cols="2">
                    <p class="text-center">
                        <small>{{ marketNegotiation.time }}</small>
                    </p>
                </b-col>

                <b-col cols="12">
                    <div v-for="tradeNegotiation in marketNegotiation.trade_negotiations ">
                        <template v-if="tradeNegotiation.sent_by_me || tradeNegotiation.sent_to_me">
                            {{ tradeNegotiation.getTradingText() }}
                            
                            <ul class="text-my-org">
                                <li>{{ tradeNegotiation.getSizeText()+" "+tradeNegotiation.quantity }}</li>
                            </ul>
                            With counterpart. Awaiting response
                        </template>
                  
                    </div>
                </b-col> 

     
 
        <ibar-trade-desired-quantity v-if="selectable" ref="popoverHit" target="popover-hit" :market-negotiation="marketNegotiation" :open="hitOpen" :is-offer="false" @close="cancelOption(false)" parent="last-negotiation"></ibar-trade-desired-quantity>

         <ibar-trade-desired-quantity v-if="selectable" ref="popoverLift" target="popover-lift" :market-negotiation="marketNegotiation" :open="liftOpen" :is-offer="true" @close="cancelOption(true)" parent="last-negotiation"></ibar-trade-desired-quantity>

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
            }
        },
        data() {
             return {
                 conditionAttr:[],
                 dialogText: '',
                 liftOpen: false,
                 hitOpen: false,
            };
        },
        watch: {
           
        },
        computed: {
            dynamicId: function(){
                return this.selectable ? "last-negotiation" : "userMarket-Negotiation-level-"+ this.marketNegotiation.id; 
            }
        },
        methods: {
            selectOption(isOffer)
            {
                if(this.marketNegotiation.is_killed) {
                    return false;
                }
                if(isOffer)
                {
                     this.liftOpen = true; 
                     this.hitOpen = false;

                }else
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
            getConditionState(marketNegotiation) {
                
                // for(var k in marketNegotiation) {

                //     if(this.conditionAttr.indexOf(k) > -1 && marketNegotiation[k] !== null)
                //     {
                //             if(typeof this.$root.config("condition_titles")[k] == "Object")
                //             {
                //                 return marketNegotiation[k] ? this.$root.config("condition_titles")[k]["true"] : this.$root.config("condition_titles")[k]["false"];   
                //             }else
                //             {
                //                 return  this.$root.config("condition_titles")[k];
                //             }
                //     }
                // }

                for(let k in this.$root.config("condition_titles")) {
                    let cond = this.$root.config("condition_titles")[k];
                    if(marketNegotiation[k] != null) {
                        if(cond.constructor == String) {
                            return cond;
                        } else {
                            return cond[new Boolean(marketNegotiation[k]).toString()];
                        }
                    }
                }

                return null;
            },
            getText(attr,marketNegotiation)
            {
                let source = marketNegotiation.getAmountSource(attr);
                if(source.id != marketNegotiation.id && marketNegotiation.is_repeat)
                {
                    return marketNegotiation.is_interest == source.is_interest || marketNegotiation.is_marker == source.is_maker ? "SPIN " + marketNegotiation[attr]  : marketNegotiation[attr];
                }
                return marketNegotiation[attr];
            },
            getStateClass(attr){

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