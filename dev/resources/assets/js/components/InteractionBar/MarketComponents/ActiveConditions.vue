<template>
    <b-container fluid dusk="ibar-conditions-active">
        <b-row v-if="activity" v-for="(negotiation, condition_type) in activity" :key="condition_type">
            <b-col cols="12" no-gutters v-for="(negotiation, negotiation_id) in negotiation" :key="negotiation_id">
                <p class="activity-block text-center" v-for="(note, type) in negotiation" :key="type">
                    {{ note }}&nbsp;&nbsp;<span class="dismiss" @click="userMarket.dismissActivity([condition_type, negotiation_id, type])">X</span>
                </p>
            </b-col>
        </b-row>
        <b-row v-if="message">
            <p class="text-center">{{ message }}&nbsp;&nbsp;<span @click="message = null">X</span></p>
        </b-row>
        <template v-if="conditions_active.length > 0">
            <b-row>
                <b-col class="text-center">
                    <strong>Received</strong>
                </b-col>
            </b-row>
            <b-row v-for="cond in conditions_active" :key="cond.condition.id">
                <b-col>
                    <component
                        :is="condition_components[cond.type]" 
                        :condition="cond"
                        :is-active="true">        
                    </component>
                </b-col>
            </b-row>
        </template>
        <template v-if="conditions_sent.length > 0">
            <b-row>
                <b-col class="text-center">
                    <strong>Sent</strong>
                </b-col>
            </b-row>
            <b-row v-for="cond in conditions_sent" :key="cond.condition.id">
                <b-col>
                    <component
                        :is="condition_components[cond.type]" 
                        :condition="cond"
                        :is-active="false">        
                    </component>
                </b-col>
            </b-row>
        </template>
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
            sent_conditions: {
                type: Array
            },
        },
        components: {
            ConditionFoKActive,
            ConditionProposalActive,
            ConditionMeetInMiddleActive,
            ConditionTradeAtBestActive
        },
        computed: {
            conditions_active: function() {
                let keys = Object.keys(this.condition_components);
                return this.conditions.filter(item => keys.indexOf(item.type) > -1);
            },
            conditions_sent: function() {
                let keys = Object.keys(this.condition_components);
                return this.sent_conditions.filter(item => keys.indexOf(item.type) > -1);
            },
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
        }
    }
</script>