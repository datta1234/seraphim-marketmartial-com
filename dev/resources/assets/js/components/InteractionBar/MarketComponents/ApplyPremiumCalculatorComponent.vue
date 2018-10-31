<template>
	<b-row>
		<b-col>
		    <b-form-checkbox  v-active-request> Apply Premium Calculator</b-form-checkbox>
		</b-col>
	</b-row>
</template>
<script>
    import UserMarketNegotiation from '../../../lib/UserMarketNegotiation';
    
    export default {
       props: {
            marketNegotiation: {
                type: UserMarketNegotiation
            }
        },
        data() {
            return {
            
            };
        },
        methods: {
           
        },
        created() {
        },
        mounted() {

        }
    }
</script>