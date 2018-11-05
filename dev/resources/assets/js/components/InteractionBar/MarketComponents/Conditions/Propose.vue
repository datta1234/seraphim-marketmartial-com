<template>
    <b-container fluid>
        <b-row>
            <b-col class="text-center mb-2">
                <strong>Proposed Level:</strong>
            </b-col>
        </b-row>
        <b-row>
            <b-col class="text-center">
                <p>
                    <b-form-input v-active-request class="input-small" v-model="marketNegotiation.bid" type="text" placeholder="Bid"></b-form-input>
                </p>
            </b-col>
            <b-col class="text-center">
                <p>
                    <b-form-input v-active-request class="input-small" v-model="marketNegotiation.offer" type="text" placeholder="Offer"></b-form-input>
                </p>
            </b-col>
        </b-row>
    </b-container>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    export default {
        name: 'condition-propose',
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation,
                default: null
            }
        }
    }
</script>