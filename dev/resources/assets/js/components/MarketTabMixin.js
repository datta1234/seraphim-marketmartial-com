export default {
    data: function () {
        return {
            market_request_state: '',
            market_request_state_label: '',
            user_market_bid: '',
            user_market_offer: '',
        }
    },
     computed: {
            current_user_market_negotiation: function() {
                let chosen_user_market = this.marketRequest.chosen_user_market;
                if(chosen_user_market){
                   return this.marketRequest.chosen_user_market.market_negotiations[chosen_user_market.market_negotiations.length -1];
                }
                return null;
            }
    },
    methods: {
        loadInteractionBar() {
            EventBus.$emit('toggleSidebar', 'interaction', true, this.marketRequest);
        },
        calcMarketState() {
            // set new refs
            this.user_market_bid = null;
            this.user_market_offer = null;
            this.market_request_state = null;
            this.market_request_state_label = null;
            
          /*  if()
            {
            }*/
            

            // run tests
            // TODO: add logic for if current user then "SENT"
            switch(this.marketRequest.attributes.state) {
                case "REQUEST-VOL":
                    
                 

                    if(this.marketRequest.sent_quote) {
                        this.market_request_state = 'request-vol';
                        this.market_request_state_label = "";
                        this.user_market_bid = this.marketRequest.sent_quote.current_market_negotiation.bid ? this.marketRequest.sent_quote.current_market_negotiation.bid : '-';
                        this.user_market_offer = this.marketRequest.sent_quote.current_market_negotiation.offer ? this.marketRequest.sent_quote.current_market_negotiation.offer: '-';
                    } else {
                        
                        this.market_request_state = 'request';
                        this.market_request_state_label = "REQUEST";
                    }
                break;
                case "REQUEST-SENT-VOL":
                    if(this.marketRequest.quotes.length > 0) {
                        this.market_request_state = 'alert';
                        this.market_request_state_label = "RECEIVED";
                    } else {
                        this.market_request_state = 'request';
                        this.market_request_state_label = "SENT";
                    }
                break;
                case "REQUEST":
                    this.market_request_state = 'request';
                    this.market_request_state_label = "REQUEST";
                break;
                case "REQUEST-SENT":
                   // this.market_request_state = 'request';
                    this.market_request_state_label = "SENT";
                break;
                case "PENDING":
                    this.market_request_state = 'negotiation-vol';
                    this.market_request_state_label = "PENDING";
                break;
                case "NEGOTIATION-VOL":
                        this.market_request_state = 'negotiation-vol';
                        this.market_request_state_label = "";
                        this.user_market_bid = this.current_user_market_negotiation != null && this.current_user_market_negotiation.bid ? this.current_user_market_negotiation.bid_display: '-';
                        this.user_market_offer = this.current_user_market_negotiation != null && this.current_user_market_negotiation.offer ? this.current_user_market_negotiation.offer_display : '-';
                break;
                case "NEGOTIATION-VOL-PENDING":
                        this.market_request_state = 'negotiation-vol-pending';
                        this.market_request_state_label = "PENDING";
                break;
                case "NEGOTIATION-OPEN-VOL":
                        this.market_request_state = 'negotiation-vol';
                        this.market_request_state_label = "";
                        this.user_market_bid = this.current_user_market_negotiation != null && this.current_user_market_negotiation.bid ? this.current_user_market_negotiation.bid_display: '-';
                        this.user_market_offer = this.current_user_market_negotiation != null && this.current_user_market_negotiation.offer ? this.current_user_market_negotiation.offer_display : '-';
                break;
                case "alert":
                    this.market_request_state = 'alert';
                    this.market_request_state_label = "";
                break;
                case "confirm":
                    this.market_request_state = 'confirm';
                    this.market_request_state_label = "";
                break;
                case "sent":
                    this.market_request_state = 'sent';
                    this.market_request_state_label = "SENT";
                break;
                default:
                    this.market_request_state = '';
                    this.market_request_state_label = '';
            }


        },
        /**
         *   toggleActionTaken - calls actionTaken() on this User Market Request
         *      when an action is required.
         */
        toggleActionTaken() {
            if(this.marketRequest.attributes.action_needed) {
                let result = this.marketRequest.actionTaken();
            }
        },
        getText(item,attr)
        {
            let source = item.getAmountSource(attr);
            if(source.id != item.id && item.is_repeat)
            {
                return item.is_interest == source.is_interest || item.is_marker == source.is_maker ? "SPIN" : item[attr];
            }
            return "100";//item[attr];
        },
        getStateClass(item,attr)
        {
            let source = item.getAmountSource(attr);
             return {
                "is-interest":source.is_interest && !source.is_my_org,
                "is-maker":source.is_maker && !source.is_my_org,
                "is-my-org":source.is_my_org
            };  
        }
    }
}