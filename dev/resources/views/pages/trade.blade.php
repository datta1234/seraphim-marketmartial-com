@extends('layouts.trade_app')

@section('content')
<div class="container-fluid trade-screen-wrapper">
	<div class="row interaction-bar-wrapper">
		<interaction-bar></interaction-bar>
		<!-- Toggle dark-theme / light-theme classes -->
		<div class="trade-section col-12 dark-theme pb-5 interaction-bar-content-pane" data-theme-wrapper>
			<user-header 
				user_name="{{ $user->full_name }}" 
				@if( isset($organisation) ) 
					organisation="{{ $organisation->title }}"
				@endif
				total_rebate="{{ $total_rebate }}">
			</user-header>

			<!-- Actions and Alerts -->
			<action-bar :markets="display_markets" :no_cares="no_cares"></action-bar>
			<!-- END Actions and Alerts -->

			<!-- Markets sections -->
			<div class="row user-markets mt-5">
				<market-group v-for="market in display_markets" :market="market" class="col"></market-group>
			</div>

			<div class="row mt-5">
				<div class="col col-lg-3 offset-lg-9">
					<div class="float-right">
						<p id="active-markets-indicator">Active Market Makers Online: <strong>7</strong></p>

		        		<!-- Rounded toggle switch -->
		        		<div class="float-right">
		        			<span class="toggle">Theme toggle</span>
							<label class="switch mb-0 ml-1" id="theme-toggle">
							  	<input type="checkbox" data-toggle-theme>
							  	<span class="slider round"></span>
							</label>
						</div>
					</div>
				</div>
			</div>

		</div>
		<chat-bar></chat-bar>
	</div>
</div>
@endsection