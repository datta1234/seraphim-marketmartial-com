import TradeConfirmationOutrightModule from './TradeConfirmationOutright';


const TradeConfirmationStructures = {
	TradeConfirmationOutright: null,
	init: () => {
		TradeConfirmationStructures.TradeConfirmationOutright = TradeConfirmationOutrightModule.init();
	}
}

export default TradeConfirmationStructures;
