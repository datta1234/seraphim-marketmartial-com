<template>
    <b-container fluid dusk="ibar-conditions-active">
        <b-row v-if="activity" v-for="(negotiation, condition_type) in activity" :key="condition_type">
            <b-col cols="12" v-for="(negotiation, negotiation_id) in negotiation" :key="negotiation_id">
                <p class="activity-block text-center" v-for="(note, type) in negotiation" :key="type">
                    {{ note }}&nbsp;&nbsp;<span class="dismiss" @click="userMarket.dismissActivity([condition_type, negotiation_id, type])">X</span>
                </p>
            </b-col>
        </b-row>
        <b-row v-if="message">
            <p class="text-center">{{ message }}&nbsp;&nbsp;<span @click="message = null">X</span></p>
        </b-row>
        <b-row v-for="cond in conditions" :key="cond.condition.id">
            <b-col>
                <component
                    :is="condition_components[cond.type]" 
                    :condition="cond">        
                </component>
            </b-col>
        </b-row>
    </b-container>
</template>
<script>

    import { EventBus } from '~/lib/EventBus';
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import UserMarket from '~/lib/UserMarket';

    import ConditionFoKActive from './ActiveConditions/FoKActive';
    import ConditionProposalActive from './ActiveConditions/ProposalActive';
    import ConditionMeetInMiddleActive from './ActiveConditions/MeetInMiddleActive';
    import ConditionTradeAtBestActive from './ActiveConditions/TradeAtBestActive';
    
    export default {
        props: {
            userMarket: {
                type: UserMarket
            },
            conditions: {
                type: Array
            },
        },
        components: {
            ConditionFoKActive,
            ConditionProposalActive,
            ConditionMeetInMiddleActive,
        },
        computed: {
            activity() {
                let valid = [
                    'proposal',
                    'meet-in-middle',
                    'condition'
                ];
                let ret = {};
                let um = this.userMarket;
                let act = um.activity;
                
                if(act)
                valid.forEach(key => {
                    if(this.userMarket.activity[key]) {
                        ret[key] = this.userMarket.activity[key];
                    }
                });
                return ret;
            }
        },
        data() {
            return {
                message: null,
                condition_components: {
                    'fok': ConditionFoKActive,
                    'proposal': ConditionProposalActive,
                    'meet-in-middle': ConditionMeetInMiddleActive,
                    'trade-at-best': ConditionTradeAtBestActive,
                }
            }
        },
        mounted() {
            EventBus.$on('notifyUser', (data) => {
                if(data.message && data.message.key == 'condition_action') {
                    this.message = data.message.data
                }
            });
            console.log('conditions', this.conditions);
        }
    }
</script>