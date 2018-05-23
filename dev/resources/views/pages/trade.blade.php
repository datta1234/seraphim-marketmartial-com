@extends('layouts.trade_app')

@section('content')
<div class="interaction-bar">
	
</div>
<!-- Toggle dark-theme / light-theme classes -->
<div class="trade-section light-theme pb-5">
	<div class="sub-nav pt-3 pb-3">
		<div class="container">
			<div class="row align-items-center">
				<div class="col col-lg-6">
					<h1>Welcome John Doe(Banks ABC)</h1>
				</div>
				<div class="col col-lg-2">
					<p class="mb-1">10:34</p>
				</div>
				<div class="col col-lg-4">
					<p class="float-right mb-1">Rebates: <strong>R65, 000</strong></p>
				</div>	
			</div>
  		</div>
	</div>
	<div class="container-fluid">

		<!-- Actions and Alerts -->
		<div class="row mt-2 menu-actions">
			<div class="col col-lg-9">
				<button type="button" class="btn mm-request-button mr-2 p-1">Request a Market</button>
				<button type="button" class="btn mm-important-button mr-2 p-1">Important</button>
				<button type="button" class="btn mm-alert-button mr-2 p-1">Alerts</button>
				<button type="button" class="btn mm-confirmation-button mr-2 p-1">Confirmations</button>
			</div>
			<div class="col col-lg-3">
				<div class="float-right">
					<button type="button" class="btn mm-transparent-button mr-2">
						<span class="icon icon-addprofile"></span> Markets
					</button>
					<button type="button" class="btn mm-transparent-button mr-2">
						<span class="icon icon-profile"></span>
					</button>
				</div>
			</div>
		</div><!-- END Actions and Alerts -->

		<!-- Markets sections -->
		<div class="watched-markets mt-5">
			<div class="row user-markets">

				<market-group v-for="derivative in display_markets" :derivative-market="derivative"></market-group>

			</div>
		</div>

		<div class="row mt-5">
			<div class="col col-lg-3 offset-lg-9">
				<p class="float-right">
            		Active Market Makers Online: <strong>7</strong>
        		</p>

        		<!-- Rounded toggle switch -->
        		<div class="float-right">
        			<span class="toggle">Light theme toggle</span>
					<label class="switch mb-0 ml-1">
					  	<input type="checkbox">
					  	<span class="slider round"></span>
					</label>
				</div>
			</div>
		</div>

	</div><!-- END Container -->
</div>
@endsection