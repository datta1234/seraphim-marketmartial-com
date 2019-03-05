<template>
    <div dusk="brokerage-fees" class="brokerage-fees" >
		<b-row>
            <b-col cols="12">
                <b-row class="mt-4">
                    <b-col cols="3" offset="9">
                        <b-form-select  id="organisations-select"
                        				class="w-100"
                                   		:options="organisation_filter"
                                   		v-model="selected_org"
                                   		@change="loadOrganisationBrokerageFees">
                        </b-form-select>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>

        <b-row v-if="brokerage_fee_data" class="mt-3">
            <b-col cols="12">
            	<b-card>
                	<b-form v-on:submit.prevent="" id="brokerage-fees-form">
	                    <b-row :key="index" v-for="(field, index) in brokerage_fee_data" class="mt-2">
	                    	<b-col v-if="index == 0 || isDifferentStructure(field.key, brokerage_fee_data[index-1].key)" cols="12" class="text-center mt-4 mb-2">
								<h3>{{ getStructure(field.key) }}</h3>
	                    	</b-col>
	                    	<b-col cols="2" offset="3">
	                            <label class="mr-sm-2" :for="'brokerage-fee-field'+index"><strong>{{ getSubTitle(field.key) }} - </strong>{{ getLabel(field.key) }}</label>
	                        </b-col>
	                        <b-col cols="4">
	                            <b-input v-model="field.value" class="w-100 mr-0" :id="'brokerage-fee-field'+index" placeholder="Enter Brokerage Fee...." />
	                        </b-col>
	                    </b-row>
	                    <b-row>
		                    <b-col cols="2" offset="10">
		                        <button type="submit" 
		                                class="btn  mm-generic-trade-button w-100 mt-5" 
		                                @click="saveOrganisationBrokerageFees()">
		                            Update
		                        </button>
		                    </b-col>
	                    </b-row>
	                </b-form>
            	</b-card>
            </b-col>
        </b-row>
        <b-row v-else class="mt-5">
        	<b-col cols="12">
        		<b-card class="text-center">
	        		<p>Select an Organisation to set it's Brokerage Fees</p>
	        	</b-card>
        	</b-col>
        </b-row>

    </div>
</template>

<script>
    export default {
    	name: 'BrokerageFees',
    	props: [
            'organisations',
        ],
        data() {
            return {
            	organisation_filter: [
                    {text: "Select an Organisation", value: null},
                ],
                selected_org: null,
                brokerage_fee_data: null,
            };
        },
        methods: {
        	loadOrganisationBrokerageFees(value) {
        		if(value !== null) {
	        		axios.get(axios.defaults.baseUrl + '/admin/brokerage-fees/'+value+'/edit')
	                .then(brokerageFeesResponse => {
	                	this.brokerage_fee_data = brokerageFeesResponse.data.data;
	                }, err => {
	                	this.$toasted.error("Failed to load the organisation's Brokerage Fees");
	                    //console.error(err);
	                });
        		}
        	},
        	saveOrganisationBrokerageFees() {
        		axios.put(axios.defaults.baseUrl + '/admin/brokerage-fees/'+ this.selected_org,
        			this.brokerage_fee_data)
                .then(brokerageFeesResponse => {
                	this.$toasted.success(brokerageFeesResponse.data.message);
                }).catch( (err) => {
                    this.$toasted.error(err.data.message);
                    //console.error(err);
                });
        	},
        	isDifferentStructure(current,previous) {
        		return current.split('.')[2] != previous.split('.')[2];
        	},
        	getStructure(key) {
        		let structure = key.split('.')[2].split('_').join(' ');
        		return structure.charAt(0).toUpperCase() + structure.slice(1);
        	},
        	getSubTitle(key) {
        		let sub_title = key.split('.')[3];
        		return sub_title.charAt(0).toUpperCase() + sub_title.slice(1);
        	},
        	getLabel(key) {
        		let structure = key.split('.')[4].split('_').join(' ');
        		return structure.charAt(0).toUpperCase() + structure.slice(1);
        	},
        },
        mounted() {
        	let organisations = JSON.parse(this.organisations);
        	if(organisations) {
        		Object.keys(organisations).forEach(id => {
        			this.organisation_filter.push({text: organisations[id], value: id});
        		});
        	}
        }
    }
</script>