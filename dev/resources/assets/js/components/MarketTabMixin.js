export default {
    data: function () {
        return {
            market_request_state: '',
            market_request_state_label: '',
        }
    },
    methods: {
        loadInteractionBar() {
            console.log("load Bar");
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
                    this.market_request_state = '';
                    this.market_request_state_label = this.marketRequest.attributes.vol_spread+" VOL SPREAD";
                break;
                case "REQUEST-SENT-VOL":
                    this.market_request_state = 'alert';
                    this.market_request_state_label = this.marketRequest.attributes.vol_spread+" VOL SPREAD";
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