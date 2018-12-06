<template>
    <div :dusk="'previous-day-market-tab-'+marketRequest.id" style="width:100%">
        <div class="col col-12 text-center">
            <div class="row">
                <b-col class="market-tab-wrapper market-tab-ratio m-auto" :class="{ 'has-ratio': marketRequest.ratio }">
                    <component class="market-tab p-3 mb-2 mt-2" :is="tabs[marketRequest.trade_structure]" :market-request="marketRequest" :no_cares="no_cares"></component>
                </b-col>
            </div>
        </div>
    </div>
</template>
<script>
    import UserMarketRequest from '~/lib/UserMarketRequest.js';
    
    export default {
        props: {
            marketRequest: {
                type: UserMarketRequest
            }
        }
    };
</script>