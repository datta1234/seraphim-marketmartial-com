<template>
    <b-input-group>
        <b-input-group-prepend is-text>
            <input type="checkbox" aria-label="Checkbox for following text input" v-model="mutableActive"
:checked="clearField()">
        </b-input-group-prepend>
        <b-form-input type="text" :name="name" aria-label="Text input with checkbox" :placeholder="placeholder" v-model="mutableValue" :disabled="!mutableActive"/>
    </b-input-group>
</template>

<script>
    export default {
        props:{
          'name': {
            type: String
          },
          'active':{
            type: Boolean
          },
          'value': {
            type: String
          },
          'placeholder': {
            type: String
          },
        },
        data() { 
            return{
                mutableActive: this.active,
                mutableValue: this.value
            }
        },
        methods: {
          clearField () {
            if(!this.mutableActive)
            {
                this.mutableValue = "";
            }
        }
        },
        mounted() {
        }
    }
</script>