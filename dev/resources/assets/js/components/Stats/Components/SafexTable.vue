<template>
    <div dusk="safex-table" class="safex-table" >
        <div class="card mt-5">
            <div class="card-header text-center">
                <h2 class="mt-2 mb-2">Rolling 6 Months of Safex Data</h2>
            </div>
            <div class="card-body">
                <b-row>
                    <b-col cols="12">
                        <b-form v-on:submit.prevent="" id="chat-message-form">
                            <b-row class="mt-4">
                                <b-col cols="1">
                                    <label class="mr-sm-2" for="stats-safex-nominal">Min. Nominal:</label>
                                </b-col>
                                <b-col cols="3">
                                    <b-form-select id="stats-safex-table"
                                               class="w-100"
                                               :options="nominal_filter"
                                               v-model="param_options.nominal">
                                    </b-form-select>
                                </b-col>
                            </b-row>
                            <b-row class="mt-2">
                                <b-col cols="1">
                                    <label class="mr-sm-2" for="stats-safex-markets">Markets:</label>
                                </b-col>
                                <b-col cols="3">
                                    <b-form-select id="stats-safex-table"
                                               class="w-100"
                                               :options="markets_filter"
                                               v-model="param_options.market">
                                    </b-form-select>
                                </b-col>
                            </b-row>
                            <b-row class="mt-2">
                                <b-col cols="1">
                                    <label class="mr-sm-2" for="stats-safex-expiration">Expiration:</label>
                                </b-col>
                                <b-col cols="3">
                                    <b-form-select id="stats-safex-expiration"
                                                   class="w-100"
                                                   :options="expiration_filter"
                                                   v-model="param_options.expiration">
                                    </b-form-select>
                                </b-col>
                            </b-row>
                            <b-row class="mt-2">
                                <b-col cols="1">
                                    <label class="mr-sm-2" for="stats-safex-search">Underlying:</label>
                                </b-col>
                                <b-col cols="3">
                                    <b-input v-model="param_options.search" class="w-100 mr-0" id="stats-safex-search" placeholder="e.g. NPN" />
                                </b-col>
                                <b-col cols="2">
                                    <button type="submit" 
                                            class="btn mm-button w-100 float-right ml-0 mr-2" 
                                            @click="loadTableData(index, false)">
                                        Filter
                                    </button>
                                </b-col>
                                <b-col cols="4" offset="2">
                                    <datepicker v-model="param_options.date"
                                                class="float-right filter-date-picker"
                                                name="safex-table-datepicker"
                                                placeholder="Select a date"
                                                :bootstrap-styling="true"
                                                :calendar-button="true"
                                                calendar-button-icon="fas fa-calendar-alt"
                                                :clear-button="true"
                                                clear-button-icon="fas fa-trash-alt">    
                                    </datepicker>
                                </b-col>
                            </b-row>
                        </b-form>
                    </b-col>
                </b-row>
                <!-- Main table element -->
                <b-table v-if="table_data_loaded && table_data[index].data != null"
                         class="mt-2 stats-table"
                         stacked="md"
                         :items="table_data[index].data"
                         :fields="table_fields"
                         :sort-by.sync="table_data[index].order_by"
                         :sort-desc.sync="table_data[index].order_ascending"
                         :no-local-sorting="true"
                         @sort-changed="(e) => sortingChanged(index, e)">
                    <template v-for="(field,key) in table_fields" :slot="field.key" slot-scope="row">
                        {{ formatItem(row.item, field.key) }}
                    </template>
                </b-table>

                <!-- <b-row v-if="table_data[index].data != null" class="justify-content-md-center">
                    <b-col md="auto" class="my-1">
                        <b-pagination @change="changePage($event, index)"
                                      :total-rows="table_data[index].total" 
                                      :per-page="table_data[index].per_page"
                                      :hide-ellipsis="true"
                                      v-model="table_data[index].current_page" 
                                      align="center"/>
                    </b-col>
                </b-row>   -->
            </div>
        </div>
    </div>
</template>

<script>
    export default {
    	props: {
            
        },
        data() {
            return {
                table_fields: [
                    { key: 'updated_at', label: 'Date' },
                    { key: 'market', label: 'Instrument' },
                    { key: 'nominal', label: 'Nominal' },
                    { key: 'strike', label: 'Strike' },
                    { key: 'volatility', label: 'Volatility' },
                    { key: 'expiration', label: 'Expiration' },
                ],
                param_options: {
                    market: null,
                    expiration: null,
                    search: null,
                    nominal: null,
                    date: null,
                    order_by: null,
                    order_ascending: true,
                },
                markets_filter: [
                    {text: "All Markets", value: null},
                ],
                expiration_filter: [
                    {text: "All Expirations", value: null},
                ],
                nominal_filter: [
                    {text: "All Nominals", value: null},
                ],
                table_data_loaded: false,
            };
        },
        methods: {
            
        },
        mounted() {
        	
        }
    }
</script>