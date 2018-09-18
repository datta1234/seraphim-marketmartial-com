<template>
    <b-row dusk="ibar-trade-request">
                   <b-col cols="10" >
        
                    <b-row>
                        <b-col cols="3" class="text-center">
                             {{ marketNegotiation.bid_qty ? marketNegotiation.bid_qty : "-"  }}
                        </b-col>
                        
                        <b-col  cols="3" class="text-center" :class="getStateClass('bid')">

                            <span v-if="selectable" @click="selectOption(true)" id="popover-hit">
                            {{ marketNegotiation.bid ? marketNegotiation.bid_display : "-"  }}
                            </span>

                            <span v-if="!selectable">
                            {{ marketNegotiation.bid ? marketNegotiation.bid_display : "-"  }}
                            </span>

                        </b-col>

                        <b-col cols="3" class="text-center" :class="getStateClass('offer')">
                            
                            <span v-if="selectable" @click="selectOption(false)" id="popover-lift">
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

     
 
        <ibar-trade-desired-quantity v-if="selectable" ref="popoverHit" target="popover-hit" :market-negotiation="marketNegotiation" :open="hitOpen" :is-bid="true" @close="cancelOption(true)"></ibar-trade-desired-quantity>

         <ibar-trade-desired-quantity v-if="selectable" ref="popoverLift" target="popover-lift" :market-negotiation="marketNegotiation" :open="liftOpen" :is-bid="false" @close="cancelOption(true)"></ibar-trade-desired-quantity>

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
   
        },
        methods: {
            selectOption(isBid)
            {
                if(isBid)
                {
                    this.liftOpen = false;
                     this.hitOpen = true;  

                }else
                {
                     this.liftOpen = true; 
                     this.hitOpen = false; 
                }
          
            },
            cancelOption(isBid)
            {
                 this.liftOpen = false
                 this.hitOpen = false;
            },
            getConditionState(marketNegotiation) {
                
                for(var k in marketNegotiation) {

                    if(this.conditionAttr.indexOf(k) > -1 && marketNegotiation[k] !== null)
                    {
                            if(typeof this.$root.config("condition_titles")[k] == "Object")
                            {
                                return marketNegotiation[k] ? this.$root.config("condition_titles")[k]["true"] : this.$root.config("condition_titles")[k]["false"];   
                            }else
                            {
                                return  this.$root.config("condition_titles")[k];
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