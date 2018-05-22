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
	<div class="container">

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

		<!-- Market Template
		<div class="watched-markets">
			<div class="row market-heading">
				<div class="col col-lg-4">
					<h2 class="text-center">MARKET HEADING</h2>	
				</div>
			</div>
			<div class="row user-markets">
				<div class="col col-lg-4 pl-5 pr-5 user-market-group">
					<div class="row mt-3 pr-3 pl-3">
						<p class="mb-1">Date</p>
						<div class="col col-lg-12 text-center market-tab p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / State
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> END Market Template -->

		<!-- Markets sections -->
		<div class="watched-markets mt-5">
			<div class="row market-heading">
				<div class="col col-lg-4">
					<h2 class="text-center">TOP 40</h2>	
				</div>
				<div class="col col-lg-4">
					<h2 class="text-center">DTOP</h2>	
				</div>
				<div class="col col-lg-4">
					<h2 class="text-center">SINGLES</h2>	
				</div>
			</div>
			<div class="row user-markets">
				<!-- TOP 40 user markets-->
				<div class="col col-lg-4 pl-5 pr-5 user-market-group">
					<!-- Date collection section -->
					<div class="row mt-3 pr-3 pl-3">
						<p class="mb-1">Date</p>
						<div class="col col-lg-12 text-center market-tab p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / <span class="user-action">State</span>
								</div>
							</div>
						</div>
						<div class="col col-lg-12 text-center market-tab market-request p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / State
								</div>
							</div>
						</div>
						<div class="col col-lg-12 text-center market-tab market-alert p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / State
								</div>
							</div>
						</div>
					</div><!-- END Date collection section -->
					<!-- Date collection section -->
					<div class="row mt-3 pr-3 pl-3">
						<p class="mb-1">Date</p>
						<div class="col col-lg-12 text-center market-tab p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / State
								</div>
							</div>
						</div>
						<div class="col col-lg-12 text-center market-tab p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									<span class="user-action">Data</span> / State
								</div>
							</div>
						</div>
						<div class="col col-lg-12 text-center market-tab market-confirm p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / State
								</div>
							</div>
						</div>
					</div><!-- END Date collection section -->
				</div><!-- END TOP 40 user markets-->
				
				<!-- DTOP user markets-->
				<div class="col col-lg-4 pl-5 pr-5 user-market-group">
					<!-- Date collection section -->
					<div class="row mt-3 pr-3 pl-3">
						<p class="mb-1">Date</p>
						<div class="col col-lg-12 text-center market-tab p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / State
								</div>
							</div>
						</div>
						<div class="col col-lg-12 text-center market-tab p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / <span class="user-action">State</span>
								</div>
							</div>
						</div>
					</div><!-- END Date collection section -->
					<!-- Date collection section -->
					<div class="row mt-3 pr-3 pl-3">
						<p class="mb-1">Date</p>
						<div class="col col-lg-12 text-center market-tab p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / State
								</div>
							</div>
						</div>
						<div class="col col-lg-12 text-center market-tab p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									<span class="user-action">Data / State</span>
								</div>
							</div>
						</div>
					</div><!-- END Date collection section -->
				</div><!-- END DTOP user markets-->

				<!-- SINGLES user markets-->
				<div class="col col-lg-4 pl-5 pr-5 user-market-group">
					<!-- Date collection section -->
					<div class="row mt-3 pr-3 pl-3">
						<p class="mb-1">Date</p>
						<div class="col col-lg-12 text-center market-tab p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / State
								</div>
							</div>
						</div>
						<div class="col col-lg-12 text-center market-tab market-confirm p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / <span class="user-action">State</span>
								</div>
							</div>
						</div>
					</div><!-- END Date collection section -->
					<!-- Date collection section -->
					<div class="row mt-3 pr-3 pl-3">
						<p class="mb-1">Date</p>
						<div class="col col-lg-12 text-center market-tab p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / State
								</div>
							</div>
						</div>
					</div><!-- END Date collection section -->
					<!-- Date collection section -->
					<div class="row mt-3 pr-3 pl-3">
						<p class="mb-1">Date</p>
						<div class="col col-lg-12 text-center market-tab market-alert p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / State
								</div>
							</div>
						</div>
						<div class="col col-lg-12 text-center market-tab market-alert p-3 mb-2 mt-2">
							<div class="row">
								<div class="col col-lg-6 market-tab-name">
									Name	
								</div>
								<div class="col col-lg-6 market-tab-state">
									Data / State
								</div>
							</div>
						</div>
					</div><!-- END Date collection section -->
				</div><!-- END SINGLES user markets-->
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