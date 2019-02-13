<template>
    <div dusk="open-interests-table" class="open-interests-table" >
        <div class="card mt-5">
            <div class="card-header text-center">
                <h2 class="mt-2 mb-2">Open Interests Data</h2>
            </div>
            <div class="card-body">
                <template v-if="table_data.data.length > 0">
                    <!-- Main table element -->
                    <b-table v-if="table_data.loaded && table_data.data != null"
                             class="mt-2 stats-table"
                             stacked="md"
                             :items="table_data.data"
                             :fields="table_data.table_fields">
                        <template v-for="(field,key) in table_data.table_fields" :slot="field.key" slot-scope="row">
                            {{ formatItem(row.item, field.key) }}
                        </template>
                    </b-table>
                </template>
                <template v-else>
                    <p class="text-center mt-5">No Open Interest Data to display</p>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
    	props: {
            tableData: {
                type: Array
            },
        },
        watch: {
            'tableData': {
                handler: function(){
                    this.table_data.loaded = false;
                    this.loadTableData();
                },
                deep: true
            },
        },
        data() {
            return {
                table_data: {
                    table_fields: [
                        { key: 'contract', label: 'Contract', sortable: false, tdClass:'text-right', thClass:'text-right' },
                        { key: 'expiry_date', label: 'ExpiryDate', sortable: false, tdClass:'text-right', thClass:'text-right' },
                        { key: 'is_put', label: 'Put/Call', sortable: true, tdClass:'text-right', thClass:'text-right' },
                        { key: 'open_interest', label: 'Open Interest', sortable: true, tdClass:'text-right', thClass:'text-right' },
                        { key: 'strike_price', label: 'Strike Price', sortable: true, tdClass:'text-right', thClass:'text-right' },
                        { key: 'delta', label: 'Delta', sortable: true, tdClass:'text-right', thClass:'text-right' },
                        { key: 'spot_price', label: 'Settlement', sortable: true, tdClass:'text-right', thClass:'text-right' },
                    ],
                    data: [],
                    pagination: {
                        current_page: 1,
                        per_page: 10,
                        total: 10,
                    },
                    loaded: false,
                }
            };
        },
        methods: {
            loadTableData() {
                this.table_data.data = this.tableData;
                this.table_data.loaded = true;
            },
            changePage($event) {
                this.table_data.pagination.current_page = $event;
                this.loadTableData();
            },
            sortingChanged(ctx) {
                this.table_data.param_options.order_by = ctx.sortBy;
                this.table_data.param_options.order_ascending = ctx.sortDesc;
                this.loadTableData();
            },
            formatItem(item, key) {
                if(item[key] == null){
                    return '-';
                }
                switch (key) {
                    case 'open_interest':
                    case 'strike_price':
                    case 'delta':
                        return this.$root.splitValHelper(item[key], ' ', 3);
                        break;
                    case 'expiry_date':
                        return moment(item[key], 'YYYY-MM-DD').format('DD MMM YYYY');
                        break;
                    case 'is_put':
                        return item[key] == 1 ? "Put" : "Call";
                        break;
                    default:
                        return item[key];
                }
            },
        },
        mounted() {
        }
    }
</script>