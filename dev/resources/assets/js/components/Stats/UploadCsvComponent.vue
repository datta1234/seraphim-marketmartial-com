<template>
    <div dusk="upload-csv" class="upload-csv" >
        <b-row class="text-center mt-5">
            <b-col>
                <button type="buttton" class="btn mm-button" @click="showModal()">Upload CSV</button>
            </b-col>
        </b-row>

        <!-- Upload Modal -->
        <b-modal class="mm-modal mx-auto" v-model="modal_data.show_modal" :ref="modal_data.modal_ref">
            <!-- Modal title content --> 
            <div class="mm-modal-title" slot="modal-title">
                Upload Safex Data CSV
            </div>
            
            <!-- Modal body content -->
            <b-row class="mt-2">
                <b-col cols="4">
                    <label class="mr-sm-2" for="stats-safex-nominal">CSV to upload:</label>
                </b-col>
                <b-col cols="8">
                    <b-form-select id="stats-safex-table"
                               class="w-100"
                               :options="csv_choice"
                               v-model="modal_data.csv_choice">
                    </b-form-select>
                </b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col cols="12">
                    <b-form-file v-model="modal_data.file" ref="csvfileinput"></b-form-file>
                </b-col>
            </b-row>

            <!-- Modal footer content -->
            <div slot="modal-footer" class="w-100">
                <b-row align-v="center">
                    <b-col cols="12">
                        <b-button :disabled="modal_data.file == null" class="mm-modal-button ml-2 w-25" @click="uploadFile()">Upload</b-button>
                        <b-button class="mm-modal-button ml-2 w-25" @click="clearFiles()">Clear</b-button>
                        <b-button dis class="mm-modal-button ml-2 w-25" @click="hideModal()">Cancel</b-button>
                    </b-col>
                </b-row>
           </div>
        </b-modal>
        <!-- END Upload Modal -->
    </div>
</template>

<script>
    export default {
        data() {
            return {
                modal_data: {
                    csv_choice: "market-activity",
                    file: null,
                    show_modal: false,
                    modal_ref: 'upload-csv-modal',
                },
                csv_choice: [
                    {text: "Safex Data", value: "market-activity"},
                    {text: "Open Interest Data", value: "open-interest"},
                ],
            };
        },
        methods: {
            /**
             * Loads the Upload CSV Modal 
             */
            showModal() {
                this.modal_data.show_modal = true;
            },
            /**
             * Closes the Upload CSV Modal 
             */
            hideModal() {
                this.modal_data.show_modal = false;
                this.clearFiles();
            },
            clearFiles() {
              this.$refs.csvfileinput.reset();
            },
            uploadFile() {
                let formData = new FormData();
                formData.append("csv_upload_file", this.modal_data.file);

                axios.post(axios.defaults.baseUrl + '/admin/stats/' + this.modal_data.csv_choice, formData)
                .then(csvUploadResponse => {
                    if(csvUploadResponse.status == 200) {
                        if(csvUploadResponse.data.success) {
                            this.hideModal();
                            this.$toasted.success(csvUploadResponse.data.message);
                        } else {
                            this.$toasted.error(csvUploadResponse.data.message);      
                        }
                    } else {
                        this.$toasted.error(csvUploadResponse.data.message);  
                    }
                }, err => {
                    console.error(err);
                });
            },
        },
        mounted() {
        	
        }
    }
</script>