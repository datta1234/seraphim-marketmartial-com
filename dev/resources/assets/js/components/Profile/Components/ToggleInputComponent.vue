<template>
    <div>
        <slot name="standard" v-if="!mutableChecked"></slot>
         <b-form-checkbox :name="name" class="mb-2 mr-sm-2 mb-sm-0" v-model="mutableChecked">{{ label }}</b-form-checkbox>
        <slot name="alt" v-if="mutableChecked"></slot>
    </div>
</template>

<script>
    export default {
        props:{
          'name': {
            type: String
          },
          'checked':{
            type: Boolean
          },
          'label':{
             type: String           
          }
        },
        data() { 
            return{
                mutableChecked: this.checked
            }
        },
        mounted() {
        }
    }
</script>