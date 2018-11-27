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
                // console.log('chosen_user_market', chosen_user_market);
                if(chosen_user_market){
                    // console.log('negotiation', this.marketRequest.chosen_user_market.market_negotiations[chosen_user_market.market_negotiations.length -1]);
                    return this.marketRequest.chosen_user_market.market_negotiations[chosen_user_market.market_negotiations.length -1];
                }
                return null;
            },
            bidState: function() {
                if(this.marketRequest.chosen_user_market)
                {
                    return this.getStateClass(this.current_user_market_negotiation,'bid');
                }else
                {
                    return {
                       'user-action': this.marketRequest.attributes.bid_state == 'action',
                    }   
                }
               
            },
            offerState: function() {
                if(this.marketRequest.chosen_user_market)
                {
                    return this.getStateClass(this.current_user_market_negotiation,'offer');
                }else
                {
                    return {
                       'user-action': this.marketRequest.attributes.offer_state == 'action',
                    }   
                }
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
           let lastTradeNegotiation  = null;

            
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
                        this.user_market_bid = this.marketRequest.sent_quote.bid ? this.marketRequest.sent_quote.bid : '-';
                        this.user_market_offer = this.marketRequest.sent_quote.offer ? this.marketRequest.sent_quote.offer: '-';
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
                        this.user_market_bid = this.current_user_market_negotiation != null && this.current_user_market_negotiation.bid ? this.current_user_market_negotiation.bid: '-';
                        this.user_market_offer = this.current_user_market_negotiation != null && this.current_user_market_negotiation.offer ? this.current_user_market_negotiation.offer : '-';
                break;
                case "NEGOTIATION-VOL-PENDING":
                        this.market_request_state = 'negotiation-vol-pending';
                        this.market_request_state_label = "";
                        this.user_market_bid = this.current_user_market_negotiation != null && this.current_user_market_negotiation.bid ? this.current_user_market_negotiation.bid: '-';
                        this.user_market_offer = this.current_user_market_negotiation != null && this.current_user_market_negotiation.offer ? this.current_user_market_negotiation.offer : '-';
                break;
                case "NEGOTIATION-OPEN-VOL":
                        this.market_request_state = 'negotiation-vol';
                        this.market_request_state_label = "";
                        if(this.current_user_market_negotiation.isTraded()) {
                            this.user_market_bid = '-';
                            this.user_market_offer = '-';
                        } else {
                            this.user_market_bid = this.current_user_market_negotiation != null && this.current_user_market_negotiation.bid ? this.current_user_market_negotiation.bid: '-';
                            this.user_market_offer = this.current_user_market_negotiation != null && this.current_user_market_negotiation.offer ? this.current_user_market_negotiation.offer : '-';
                        }
                break;
                case "TRADE-NEGOTIATION-SENDER":
                case "TRADE-NEGOTIATION-COUNTER":
                case "TRADE-NEGOTIATION-BALANCER":
                    this.market_request_state = 'trade-negotiation-open';
                    
                        if(this.marketRequest.chosen_user_market && this.marketRequest.chosen_user_market.isTradingAtBest()) {
                            this.market_request_state_label = "";
                            this.user_market_bid = this.current_user_market_negotiation != null && this.current_user_market_negotiation.bid ? this.current_user_market_negotiation.bid: '-';
                            this.user_market_offer = this.current_user_market_negotiation != null && this.current_user_market_negotiation.offer ? this.current_user_market_negotiation.offer : '-';
                        } else {

                            if(this.current_user_market_negotiation) {
                                lastTradeNegotiation = this.current_user_market_negotiation.getLastTradeNegotiation();
                            }

                            if(lastTradeNegotiation) {
                               this.market_request_state_label = lastTradeNegotiation.getVolLevel();
                            }
                        }
                       
                  break;
                  case "TRADE-NEGOTIATION-PENDING":
                       this.market_request_state = 'trade-negotiation-pending';
                       
                       if(this.current_user_market_negotiation)
                       {
                            lastTradeNegotiation = this.current_user_market_negotiation.getLastTradeNegotiation();
                       }
                       if(lastTradeNegotiation)
                       {
                           this.market_request_state_label = lastTradeNegotiation.getVolLevel();
                       }
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
            // {
            //     return item.is_interest == source.is_interest || item.is_marker == source.is_maker ? "SPIN" : item[attr];
            // }
            return item[attr];
        },
        getStateClass(item,attr)
        {
            if(item[attr] == null) {
                return "";
            }
            let source = item.getAmountSource(attr);
             return {
                "is-interest":source.is_interest && !source.is_my_org,
                "is-maker":source.is_maker && !source.is_my_org,
                "is-my-org":source.is_my_org
            };  
        }
    }
}