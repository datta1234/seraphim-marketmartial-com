<template>
    <b-row dusk="ibar-trade-request">
                   <b-col cols="10" >
        
                    <b-row>
                        <b-col cols="3" class="text-center">
                             {{ marketNegotiation.bid_qty ? marketNegotiation.bid_qty : "-"  }}
                        </b-col>
                        
                        <b-col  @click="selectOption(marketNegotiation,true)" cols="3" class="text-center" :class="getStateClass('bid',marketNegotiation)">

                            <span v-if="selectable" @click="selectOption(marketNegotiation,false)" id="popover-hit">
                            {{ marketNegotiation.bid ? marketNegotiation.bid_display : "-"  }}
                            </span>

                            <span v-if="!selectable">
                            {{ marketNegotiation.bid ? marketNegotiation.bid_display : "-"  }}
                            </span>

                        </b-col>

                        <b-col cols="3" class="text-center" :class="getStateClass('offer',marketNegotiation)">
                            
                            <span v-if="selectable" @click="selectOption(marketNegotiation,false)" id="popover-lift">
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

     
       <b-popover v-if="selectable"  ref="popoverHit" target="popover-hit" placement="bottom">
            <b-row>
                <b-col cols="12" class="text-center">
                Hit the Bid?
                </b-col>
            </b-row>
            <b-row>
               <b-col cols="6">
                    <b-btn variant="danger" @click="cancelSelect(true)">Cancel</b-btn>
               </b-col>
               <b-col cols="6">
                     <b-btn variant="success">Sell</b-btn>
               </b-col>
            </b-row>
      </b-popover>

       <b-popover v-if="selectable"  ref="popoverLift" target="popover-lift" placement="bottom">
            <b-row>
                <b-col cols="12" class="text-center" @click="cancelSelect(false)">
                Lift the offer?
                </b-col>
            </b-row>
            <b-row>
               <b-col cols="6">
                    <b-btn variant="danger"  @click="cancelSelect(false)">Cancel</b-btn>
               </b-col>
               <b-col cols="6">
                     <b-btn variant="success">buy</b-btn>
               </b-col>
            </b-row>
      </b-popover>

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
                 isBid:false,
            };
        },
        watch: {
           
        },
        computed: {
   
        },
        methods: {
            cancelSelect(isBid){
                console.log(isBid);
                this.isBid ? this.$refs.popoverHit.$emit('close') : this.$refs.popoverLift.$emit('close');
            },
            selectOption(marketNegotiation,isBid)
            {
                this.isBid = isBid;
                !this.isBid ? this.$refs.popoverHit.$emit('close') : this.$refs.popoverLift.$emit('close');
                this.isBid ? this.$refs.popoverHit.$emit('open') : this.$refs.popoverLift.$emit('open');
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
            getStateClass(attr,marketNegotiation){

                let source = marketNegotiation.getAmountSource(attr);
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