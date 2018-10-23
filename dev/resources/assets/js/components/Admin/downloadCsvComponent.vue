<template>
    <div dusk="download-csv" class="download-csv" >
        <button type="button" 
                class="btn mm-generic-trade-button w-100"
                @click="showModal()">
            {{ button_text }}
        </button>

        <!-- Confirmations Modal -->
        <b-modal class="mm-modal mx-auto" v-model="modal_data.show_modal" :ref="modal_data.modal_ref">
            <!-- Modal title content --> 
            <div class="mm-modal-title" slot="modal-title">
                Download Csv
            </div>

            <b-form :action="endpointUrl" method="GET" ref="downloadCsvForm">
                <b-row v-if="is_rebate" class="mt-2">
                    <b-col cols="4">
                        <label class="mr-sm-2" for="admin-download-paid-filter">Paid Status:</label>
                    </b-col>
                    <b-col cols="8">
                        <b-form-select id="admin-download-paid-filter"
                                       class="w-100"
                                       :options="paid_options"
                                       v-model="modal_data.paid_filter"
                                       name="is_paid">
                        </b-form-select>
                    </b-col>
                </b-row>
                <b-row v-else class="mt-2">
                    <b-col cols="4">
                        <label class="mr-sm-2" for="admin-download-paid-filter">Booking Status:</label>
                    </b-col>
                    <b-col cols="8">
                        <b-form-select id="admin-download-status-filter"
                                       class="w-100"
                                       :options="status_options"
                                       v-model="modal_data.status_filter"
                                       name="is_confirmed">
                        </b-form-select>
                    </b-col>
                </b-row>
                <b-row class="mt-2">
                    <b-col cols="4">
                        <label class="mr-sm-2" for="admin-download-organisation-filter">
                            Organisation:
                        </label>
                    </b-col>
                    <b-col cols="8">
                        <b-form-select id="admin-download-organisation-filter"
                                       class="w-100"
                                       :options="organisation_filter"
                                       v-model="modal_data.organisation"
                                       name="organisation">
                        </b-form-select>
                    </b-col>
                </b-row>
                <b-row class="mt-2">
                    <b-col cols="4">
                        <label class="mr-sm-2" for="admin-download-expiration-filter">
                            Expiration:
                        </label>
                    </b-col>
                    <b-col cols="8">
                        <b-form-select id="admin-download-expiration-filter"
                                       class="w-100"
                                       :options="expiration_filter"
                                       v-model="modal_data.expiration"
                                       name="expiration">
                        </b-form-select>
                    </b-col>
                </b-row>
                <b-row class="mt-2">
                    <b-col cols="4">
                        <label class="mr-sm-2" for="admin-download-date-filter">
                            Date:
                        </label>
                    </b-col>
                    <b-col cols="8">
                        <datepicker id="admin-download-date-filter"
                                    v-model="modal_data.date_filter"
                                    class="filter-date-picker"
                                    name="date"
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

            <!-- Modal footer content -->
            <div slot="modal-footer" class="w-100">
                <b-row align-v="center">
                    <b-col cols="12">
                        <b-button class="mm-modal-button ml-2 w-25" @click="downloadCsv()">
                            Download
                        </b-button>
                        <b-button class="mm-modal-button ml-2 w-25" @click="hideModal()">
                            Cancel
                        </b-button>
                    </b-col>
                </b-row>
           </div>
        </b-modal>
        <!-- END Confirmations Modal -->
    </div>
</template>

<script>
    export default {
        props:{
          'button_text': {
            type: String
          },
          'end_point': {
            type: String
          },
          'is_rebate': {
            type: Boolean
          }
        },
        computed: {
            endpointUrl() {
                return axios.defaults.baseUrl + this.end_point;
            }
        },
        data() {
            return {
                modal_data: {
                    expiration: null,
                    organisation: null,
                    date_filter: null,
                    paid_filter: null,
                    status_filter: null,
                    show_modal: false,
                    modal_ref: 'csv-options-modal',
                },
                paid_options: [
                    {text: "All", value: null},
                    {text: "Paid", value: '1'},
                    {text: "Not Paid", value: '0'},
                ],
                status_options: [
                    {text: "All", value: null},
                    {text: "Pending", value: '0'},
                    {text: "Confirmed", value: '1'},
                ],
                organisation_filter: [
                    {text: "All", value: null},
                ],
                expiration_filter: [
                    {text: "All Expirations", value: null},
                ],
            };
        },
        methods: {
            showModal() {
                this.modal_data.show_modal = true;
            },
            hideModal() {
                this.modal_data.show_modal = false;
            },
            downloadCsv() {
                this.$refs.downloadCsvForm.submit();
                this.modal_data.show_modal = false;
            },
            loadExpirations() {
                axios.get(axios.defaults.baseUrl + '/trade/safex-expiration-date', {
                    params:{
                        'not_paginate': true,
                    }
                })
                .then(expirationsResponse => {
                    if(expirationsResponse.status == 200) {
                        Object.keys(expirationsResponse.data).forEach(key => {
                            this.expiration_filter.push(moment(expirationsResponse.data[key].date).format('DD MMM YYYY'));
                        });
                    } else {
                        console.error(err); 
                    }
                }, err => {
                    console.error(err);
                });
            },
            loadOrganisations() {
                axios.get(axios.defaults.baseUrl + '/admin/organisation')
                .then(organisationResponse => {
                    if(organisationResponse.status == 200) {
                        Object.keys(organisationResponse.data).forEach(key => {
                            this.organisation_filter.push({
                                text: organisationResponse.data[key],
                                value: key
                            });
                        });
                    } else {
                        console.error(err); 
                    }
                }, err => {
                    console.error(err);
                });
            },
        },
        mounted() {
            this.loadExpirations();
            this.loadOrganisations();
        }
    }
</script>