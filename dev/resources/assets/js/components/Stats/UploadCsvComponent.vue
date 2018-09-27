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
            <b-row>
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
                    file: null,
                    show_modal: false,
                    modal_ref: 'upload-csv-modal',
                },
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
                console.log("Uploading file: ", this.modal_data.file);
                let formData = new FormData();
                formData.append("safex_csv_file", this.modal_data.file);
                
                axios.post(axios.defaults.baseUrl + '/admin/stats/market-activity', formData)
                .then(csvUploadResponse => {
                    if(csvUploadResponse.status == 200) {
                        console.log("Upload response: ", csvUploadResponse);
                        this.hideModal();
                        this.$toasted.success(csvUploadResponse.data.message);
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