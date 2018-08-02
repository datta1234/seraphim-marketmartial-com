export default {
    data: function () {
        return {
            market_request_state: '',
            market_request_state_label: '',
            user_market_bid: '',
            user_market_offer: '',
        }
    },
    methods: {
        loadInteractionBar() {
            EventBus.$emit('toggleSidebar', 'interaction', true, this.marketRequest);
        },
        calcMarketState() {
            // set new refs
            // this.user_market = this.marketRequest.getChosenUserMarket();
            // this.current_negotiation = this.user_market ? this.user_market.getCurrentNegotiation() : null;
            // this.user_market_bid = this.current_negotiation ? this.current_negotiation.bid : null;
            // this.user_market_offer = this.current_negotiation ? this.current_negotiation.offer : null;
            
            // run tests
            // TODO: add logic for if current user then "SENT"
            switch(this.marketRequest.attributes.state) {
                case "REQUEST-VOL":
                    if(this.marketRequest.user_market) {
                        this.market_request_state = 'request-vol';
                        this.market_request_state_label = "";
                        this.user_market_bid = this.marketRequest.user_market.current_market_negotiation.bid ? this.marketRequest.user_market.current_market_negotiation.bid : '-';
                        this.user_market_offer = this.marketRequest.user_market.current_market_negotiation.offer ? this.marketRequest.user_market.current_market_negotiation.offer : '-';
                    } else {
                        this.market_request_state = 'request';
                        this.market_request_state_label = "REQUEST";
                    }
                break;
                case "REQUEST-VOL-HOLD":
                    if(this.marketRequest.user_market) {
                        this.market_request_state = 'alert';
                        this.market_request_state_label = "";
                        this.user_market_bid = this.marketRequest.user_market.current_market_negotiation.bid ? this.marketRequest.user_market.current_market_negotiation.bid : '-';
                        this.user_market_offer = this.marketRequest.user_market.current_market_negotiation.offer ? this.marketRequest.user_market.current_market_negotiation.offer : '-';
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
                    this.market_request_state = 'request';
                    this.market_request_state_label = "SENT";
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
        }
    }
}