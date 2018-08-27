<template>
    <b-row dusk="ibar-negotiation-history-contracts" class="ibar-negotiation-history-contracts">
        <b-col>
            <b-row v-for="item in history" v-if="item.id != ''">            
                <b-col cols="10" >
                    <b-row>
                        <b-col cols="3" class="text-center">
                             {{ item.bid_qty ? item.bid_qty : "-"  }}
                        </b-col>
                        <b-col cols="3" class="text-center" :class="getStateClass('bid',item)">
                            {{ item.bid ? item.bid : "-"  }}
                        </b-col>
                        <b-col cols="3" class="text-center" :class="getStateClass('offer',item)">
                            {{ item.offer ? item.offer : "-"  }}
                        </b-col>
                        <b-col cols="3" class="text-center">
                             {{ item.offer_qty ? item.offer_qty : "-"  }}
                        </b-col>
                    </b-row>
                    <b-row> 
                        <b-col class="condition text-center" cols="6"> <small>{{ getConditionState(item) }} </small></b-col>
                        <b-col class="condition text-center" cols="6"> <small>{{ getConditionState(item) }} </small></b-col>
                    </b-row>
                </b-col>
                <b-col cols="2">
                    <p class="text-center">
                        <small>{{ item.time }}</small>
                    </p>
                </b-col>
            </b-row>
            <b-row class="justify-content-md-center" v-if="message">
                <b-col class="mt-2">
                    <p class="text-center">
                        <small >{{ message }}</small>
                    </p>
                </b-col>
            </b-row>
       
        
        </b-col>
    </b-row>
</template>


<script>
    export default {
        data(){
            return {
                 conditionAttr:[],
            };
        },
        props: {
            history: {
                type: Array
            },
            message:{
                type: String
            }
        },
        methods: {
            getConditionState(item){
                
                for(var k in item) {

                    if(this.conditionAttr.indexOf(k) > -1 && item[k] !== null)
                    {
                            if(typeof this.$root.config("condition_titles")[k] == "Object")
                            {
                                return item[k] ? this.$root.config("condition_titles")[k]["true"] : this.$root.config("condition_titles")[k]["false"];   
                            }else
                            {
                                return  this.$root.config("condition_titles")[k];
                            }
                    }
                }

                return null;
            },
            getStateClass(attr,item){

                // recursvley run the method to set the state color based on the prev response
                let prevItem = null;
                if(item.market_negotiation_id !== null)
                {
                    prevItem = this.history.find((itItem) => item.market_negotiation_id == itItem.id );
                }

                if(typeof prevItem !== "undefined" &&  prevItem != null && prevItem.market_negotiation_id != prevItem.id  && prevItem[attr] == item[attr])
                {
                 return this.getStateClass(attr,prevItem);   
                }else
                {
                     return {
                        "is-interest":item.is_interest && !item.is_my_org,
                        "is-maker":item.is_maker && !item.is_my_org,
                        "is-my-org":item.is_my_org
                    };  
                }

                         
            }
        },
        mounted() {       
            this.conditionAttr = Object.keys(this.$root.config("condition_titles"));
        }
    }
</script>