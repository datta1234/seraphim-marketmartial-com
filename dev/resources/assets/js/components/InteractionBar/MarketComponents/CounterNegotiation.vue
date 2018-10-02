<template class="testingClass">
       <b-popover ref="popover" @show="addCustomClass" :target="target" :placement="placement" :show.sync="open" triggers="" :container="parent" class="testingClass2">
            <template slot="title">
               <div class="text-center"><strong>Counter</strong>
                  <a @click="cancel" class="close" aria-label="Close">
                    <span class="d-inline-block" aria-hidden="true">&times;</span>
                  </a>
               </div>
            </template>
            <template>
              <b-row>
                <b-col class="text-center">
                    <p>
                        <b-form-input class="input-small" v-model="proposed_market_negotiation.bid" type="text" placeholder="Bid"></b-form-input>
                    </p>
                </b-col>
                <b-col class="text-center">
                    <p>
                        <b-form-input class="input-small" v-model="proposed_market_negotiation.offer" type="text" placeholder="Offer"></b-form-input>
                    </p>
                </b-col>
              </b-row>
              <b-row>
                    <b-col cols="12" v-for="(error,key) in errors" :key="key" class="text-danger">
                        {{ error[0] }}
                    </b-col>
                    <b-col cols="12">       
                        <b-btn variant="primary" class="btn-block mt-2" :disabled="server_loading" @click="storeCounter()">Send Counter</b-btn>
                    </b-col>
                </b-row>
            </template>
      </b-popover>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';

    export default {
        name: 'ibar-counter-negotiation',
        props: {
            marketNegotiation: {
              type: UserMarketNegotiation,
              default: null,
            },
            target: {
              type: String,
              default: null,
            },
            open: {
              type: Boolean,
              default: false,
            },
            parent: {
              type: String,
              default: "",
            },
            placement: {
              type: String,
              default: "bottom",
            },
            popoverClass: {
              type: String,
              default: null
            }
        },
        data() {
            return {
              proposed_market_negotiation: new UserMarketNegotiation(),
              errors: null,
              server_loading: false,
            };
        },
        computed: {
         
        },
        methods: {
          addCustomClass(evt) {
            if(this.popoverClass && evt.relatedTarget && evt.relatedTarget.nodeType === Node.ELEMENT_NODE) {
              evt.relatedTarget.classList.add(this.popoverClass);
            }
          },
          cancel(){
              this.$emit('close');
          },
          storeCounter() {
            this.server_loading = true;
            this.marketNegotiation.counterNegotiation(this.proposed_market_negotiation)
            .then(data => {
              this.server_loading = false;
              console.log(data);
            })
            .catch(err => {
              this.server_loading = false;
              console.log(err);
            });
          }
        },
        mounted() {
          // addAttachmentClass
        }
    }
</script>
