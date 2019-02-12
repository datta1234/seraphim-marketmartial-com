<template>
    <div dusk="terms-and-conditions" class="terms-and-conditions">
        <template v-if="!completed">
            <p>
                After submitting this information, the Market Martial team will verify your profile.
            </p>
            <p>
                You can view your account details at any time.
            </p>

            <b-form v-on:submit.prevent="" id="index-details-form">
            
                <b-form-checkbox id="checkbox1"
                                 v-model="accepted"
                                 :value="true"
                                 :unchecked-value="false">
                    I accept the <a target="_blank" v-bind:href="privacyPolicyLink">Privacy Policy</a> and <a target="_blank" v-bind:href="termsOfUseLink">Terms of use</a>
                </b-form-checkbox>

                <b-row v-if="errors.length > 0" class="mt-4">
                    <b-col :key="index" v-for="(error, index) in errors" cols="12">
                        <p class="text-danger mb-0">{{ error }}</p>
                    </b-col>
                </b-row>
                
                <b-form-group class="form-group row mb-0">
                    <div class="col-sm-12 col-md-3 offset-md-6 col-xl-2 offset-xl-8 mt-2">
                        <b-button id="submit-index-details" 
                                  type="submit" 
                                  class="btn mm-button" 
                                  @click="update()"
                                  :disabled="!accepted">
                            Next
                        </b-button>
                    </div>
                </b-form-group>
            </b-form>
        </template>
        <template v-else>
            <p>Please wait for your account to be verified.</p>
        </template>
    </div>
</template>

<script>
    export default {
        props:{
            'tcAccepted': {
                type: Boolean
            },
            privacyPolicyLink: {
                type: String
            },
            termsOfUseLink: {
                type: String
            }
        },
        data() {
            return {
            	errors: [],
                accepted: false,
                completed: false,
            };
        },
        methods: {
            update() {
                axios.put(axios.defaults.baseUrl + '/terms-and-conditions/',
                    {tc_accepted: this.accepted})
                .then(tsCsResponse => {
                    this.completed = true;
                    this.$toasted.success(tsCsResponse.data.message);
                }, err => {
                    this.errors = err.errors.tc_accepted;
                    this.$toasted.error(err.message);
                    console.error(err);
                });
            },
        },
        mounted() {
    		console.log("YEET", this.tcAccepted);
            this.accepted = this.tcAccepted;
            this.completed = this.tcAccepted;
        }
    }
</script>