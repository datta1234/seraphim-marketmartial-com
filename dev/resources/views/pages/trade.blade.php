@extends('layouts.trade_app')

@section('content')
<div class="container-fluid trade-screen-wrapper">
	<b-row class="interaction-bar-wrapper">
		<interaction-bar></interaction-bar>
		<!-- Toggle dark-theme / light-theme classes -->
		<b-col v-bind:class="tradeTheme" class="trade-section pb-5 interaction-bar-content-pane">
			<user-header 
				user_name="{{ $user->full_name }}" 
				
				@if( isset($organisation) ) 
					organisation="{{ $organisation->title }}"
				@endif
				
				:total_rebate="{{ $total_rebate }}">
			</user-header>
			
			<!-- Actions and Alerts -->
			<action-bar :trade_confirmations="trade_confirmations" :markets="display_markets" :no_cares="no_cares"></action-bar>
			<!-- END Actions and Alerts -->
			<!-- Markets sections -->
			<!-- <VuePerfectScrollbar ref="barContent" :options="scroll_settings" class="interaction-scrollable"> -->
			<b-row class="user-markets mt-3">
				<mm-loader :default_state="true" event_name="pageLoaded" width="200" height="200"></mm-loader>
				<market-group v-if="page_loaded" v-for="(market, market_index) in display_markets" :market="market" :no_cares="no_cares" class="col"></market-group>
			</b-row>
			<!-- </VuePerfectScrollbar> -->

			<b-row align-v="end" class="mt-5">
				<div class="col col-lg-3 offset-lg-9">
					<div class="float-right">
						<p id="active-markets-indicator">Active Market Makers Online: <strong><active-makers></active-makers></strong></p>

		        		<!-- Rounded toggle switch -->
		        		<theme-toggle></theme-toggle>
					</div>
				</div>
			</b-row>

		</b-col>
		<chat-bar></chat-bar>
	</b-row>
</div>
@endsection