<template>
    <b-container fluid dusk="ibar-conditions-active">
        <b-row v-if="message">
            <p class="text-center">{{ message }}&nbsp;&nbsp;<span @click="message = null">X</span></p>
        </b-row>
        <b-row v-for="cond in conditions" :key="cond.condition.id">
            <component
                :is="condition_components[cond.type]" 
                :condition="cond">        
            </component>
        </b-row>
    </b-container>
</template>
<script>

    import { EventBus } from '~/lib/EventBus';
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    
    import ConditionFoKActive from './ActiveConditions/FoKActive';
    import ConditionProposalActive from './ActiveConditions/ProposalActive';
    import ConditionMeetInMiddleActive from './ActiveConditions/MeetInMiddleActive';
    
    
    export default {
        props: {
            conditions: {
                type: Array
            },
        },
        components: {
            ConditionFoKActive,
            ConditionProposalActive,
            ConditionMeetInMiddleActive,
        },
        data() {
            return {
                message: null,
                condition_components: {
                    'fok': ConditionFoKActive,
                    'proposal': ConditionProposalActive,
                    'meet-in-middle': ConditionMeetInMiddleActive,          
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