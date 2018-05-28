<template>
	<div>
		<div class="form-group row" v-for="email in emailSettingsDataJson">
			<div class="col-sm-12 col-md-4">
			
			<input placeholder="Enter your email here..." name="email[3][email]" type="email" v-model="email.email" class="form-control">

			</div>
		</div>


 <div>
    <b-button @click="showModal">
      Open Modal
    </b-button>
    <b-modal ref="myModalRef" hide-footer title="Using Component Methods">
      <div class="d-block text-center">
        <h3>Hello From My Modal!</h3>
      </div>
      <b-btn class="mt-3" variant="outline-danger" block @click="hideModal">Close Me</b-btn>
    </b-modal>
  </div>

	</div>


</template>

<script>

    export default {
        props: ['emailSettingsData'],
        data() {
            return {
            	emailSettingsDataJson: function(){
            		return JSON.parse(this.emailSettingsData);
            	}
            };
        },
        computed: {

        },
        methods: {
  
        },
        mounted() {

        	for (var i = 0; i <= this.emailSettingsDataJson.length; i++) {
        		console.log(this.emailSettingsDataJson[i])
        	}
        	console.log("this is the content");
        }
    }
</script>