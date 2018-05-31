<template>
<div>

<form>
        <b-form-group
         v-for="(email, index) in emailSettingForm.data().email"
          horizontal
          :label-cols="4"
          :label=" email.title ? email.title : email.default_label.title"
          :label-for="'email-'+index+'-email'"
          :invalid-feedback="emailSettingForm.errors.get('email.'+index+'.email')"
      >
        <b-form-input  :id="'email-'+index+'-email'"  :state="emailSettingForm.errors.state('email.'+index+'.email')" v-model="email.email"></b-form-input>
      </b-form-group>
</form>


    <b-button @click="showModal">
     Add E-mail
    </b-button>

    <b-btn class="mt-3 mm-button" @click="update">
       Update
    </b-btn>


    <b-modal ref="myModalRef" hide-footer title="Add Email">
      <div class="d-block">
         <b-form-group
              :label-for="'email-title'"
              placeholder="Place email"
              :invalid-feedback="email.errors.get('title')">
            <b-form-input  :id="'email-'+index+'-title'"  placeholder="Email label" :state="email.errors.state('title')" v-model="email.title"></b-form-input>
          </b-form-group>

        <b-form-group
        :label-for="'email-email'"
        placeholder="Place email"
        :invalid-feedback="email.errors.get('email')">
        <b-form-input  :id="'email-'+index+'-email'"  placeholder="Email Address" :state="email.errors.state('email')" v-model="email.email"></b-form-input>
        </b-form-group>
      </div>
      <b-btn class="mt-3 mm-button" block @click="hideModal">Save</b-btn>
    </b-modal>
 
</div>


</template>

<script>
    const Form = require('../lib/Form.js');

    export default {
        props:{
          'emailSettingsData': {
            type: Array
          },
          'defaultLabelsData':{
            type: Array
          }
        },
        data() { 
            return {          
                email : new Form({
                    email: '',
                    title: ''
                }),
                emailSettingForm: new Form(),
                mutableEmailSettingsData: this.emailSettingsData,
            }
        },
        methods: {
            update() {
                this.emailSettingForm.put('/email-settings')
                .then((response) => {
                    this.mutableEmailSettingsData = response.data;
                    this.fields = [];
                    // fields will be update from the server
                    this.emailSettingForm.updateData({email:this.mutableEmailSettingsData});
                });
                
            },
            showModal () {
                this.$refs.myModalRef.show()
            },
            hideModal () {
                this.email.post('/email-settings')
                .then((response) => {
                    this.mutableEmailSettingsData.push(response.data);
                    this.emailSettingForm.updateData({email:this.mutableEmailSettingsData});
                    this.$refs.myModalRef.hide();
                });
            },
        },
        mounted() {
            //load the defaults as users ones
            this.defaultLabelsData.forEach((label)=>{
                this.mutableEmailSettingsData.push({
                    'title': label.title,
                    'default_id':label.id,
                    'notifiable':false,
                    'email':null
                });
            });
            this.emailSettingForm.updateData({email:this.mutableEmailSettingsData});
        }
    }
</script>