<template>
    <b-container fluid dusk="ibar-conditions-active">
        <b-row v-for="(cond, idx) in conditions" :key="idx">
            <component
                :is="condition_components[cond.type]" 
                :condition="cond">        
            </component>
        </b-row>
    </b-container>
</template>
<script>
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
                condition_components: {
                    'fok': ConditionFoKActive,
                    'proposal': ConditionProposalActive,
                    'meet-in-middle': ConditionMeetInMiddleActive,          
                }
            }
        },
        mounted() {
            console.log('conditions', this.conditions);
        }
    }
</script>