<template>
<div>

  <form>
          <b-form-group

           v-for="(email,index) in emailSettingForm.data().email"
            horizontal
            :key = "index"
            :label-cols="4"
            :label=" email.title ? email.title : email.default_label.title"
            :label-for="'email-'+index+'-email'"
            :invalid-feedback="emailSettingForm.errors.get('email.'+index+'.email')"
        >
          <b-form-input  :id="'email-'+index+'-email'"  :state="emailSettingForm.errors.state('email.'+index+'.email')" v-model="email.email"></b-form-input>
        </b-form-group>
  </form>

  <div class="row">
    <div class="col col-sm-12 col-md-12 col-lg-10 offset-lg-2">
        <b-button id="update-btn" class="mm-button mm-base float-right w-25 ml-2" @click="update">
         {{ (profileComplete == false && isAdmin == false) ? "Next" : "Update"  }}
      </b-button>

      <b-button  class="mm-button mm-base float-right w-25" @click="showModal">
       Add E-mail
      </b-button>
    </div>
  </div>
    


  <b-modal ref="myModalRef" hide-footer title="Add Email">
    <div class="d-block">
       <b-form-group
            :label-for="'email-title'"
            placeholder="Place email"
            :invalid-feedback="email.errors.get('title')">
          <b-form-input  :id="'email-title'"  placeholder="Email label" :state="email.errors.state('title')" v-model="email.title"></b-form-input>
        </b-form-group>

      <b-form-group
      :label-for="'email-email'"
      placeholder="Place email"
      :invalid-feedback="email.errors.get('email')">
      <b-form-input  :id="'email-email'"  placeholder="Email Address" :state="email.errors.state('email')" v-model="email.email"></b-form-input>
      </b-form-group>
    </div>
    <b-btn class="mt-3 mm-button" block @click="hideModal"> Save</b-btn>
  </b-modal>
 
</div>


</template>

<script>
    const Form = require('~/lib/Form.js');
    export default {
        props:{
            'emailSettings': {
                type: String
            },
            'defaultLabels':{
                type: String
            },
            'profileComplete':{
                type: Boolean
            },
            'isAdmin':{
                type: Boolean
            },
            'user':{
                type: Object
            },
        },
        data() { 
            return {          
                email: new Form({
                    email: '',
                    title: ''
                }),
                emailSettingForm: new Form(),
                mutableEmailSettingsData: []

            }
        },
        methods: {
            update() {
                this.emailSettingForm.put(axios.defaults.baseUrl + (this.isAdmin ? '/admin/user/email-settings/' + this.user.id : '/email-settings') )
                .then((response) => {
                    this.mutableEmailSettingsData = response.data.email;
                    this.fields = [];
                    // fields will be update from the server
                    this.emailSettingForm.updateData({email:this.mutableEmailSettingsData});
                    console.log(response.data);
                    this.$toasted.success(response.message);
                    
                    if(this.isAdmin == false && this.profileComplete == false)
                    {
                        window.location.href = response.data.redirect;
                    }

                });
                
            },
            showModal () {
                this.$refs.myModalRef.show()
            },
            hideModal () {
                this.email.post(axios.defaults.baseUrl + (this.isAdmin ? '/admin/user/email-settings/' + this.user.id : '/email-settings'))
                .then((response) => {
                    this.mutableEmailSettingsData.push(response.data);
                    this.emailSettingForm.updateData({email:this.mutableEmailSettingsData});
                    this.$refs.myModalRef.hide();
                    this.$toasted.success(response.message);
                });
            }
        },
        mounted() {
            //load the defaults as users ones
            this.defaultLabelsData = JSON.parse(this.defaultLabels);
            this.emailSettingsData = JSON.parse(this.emailSettings);
            

            this.defaultLabelsData.forEach((label)=>{
                this.mutableEmailSettingsData.push({
                    'title': label.title,
                    'default_id':label.id,
                    'notifiable':false,
                    'email':null
                });
            });

            this.mutableEmailSettingsData = this.mutableEmailSettingsData.concat(this.emailSettingsData);
            console.log(this.mutableEmailSettingsData);

            this.emailSettingForm.updateData({email:this.mutableEmailSettingsData});
            console.log("user: ",this.user.id);
        }
    }
</script>