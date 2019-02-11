<template>
    <b-row dusk="ibar-user-market-title">
        <b-col cols="10" v-if="$root.is_admin">
            <h5 style="cursor:pointer;" @click="openModal()">{{ title }}</h5>
            <info-modal :market-request="marketRequest" ref="infoModal"></info-modal>
        </b-col>
        <b-col cols="10" v-else>
            <h5 class="">{{ title }}</h5>
        </b-col>
        <b-col cols="2">
            <p class="pull-right">{{ time }}</p>
        </b-col>
    </b-row>
</template>

<script>
    import UserMarketRequest from '~/lib/UserMarketRequest';
    import MarketRequestInfoModal from '~/components/Admin/MarketRequestInfoModal';
    export default {
        components: {
            'info-modal': MarketRequestInfoModal
        },
        props: {
            marketRequest: UserMarketRequest,
            title: {
                type: String,
                default: ""
            },
            time: {
                type: String,
                default: ""
            }
        },
        methods: {
            openModal() {
                this.$refs.infoModal.showModal();
            }
        },
        mounted() {

        }
    }
</script>