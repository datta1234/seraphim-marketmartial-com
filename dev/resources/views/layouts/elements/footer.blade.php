<footer id="main-footer">
	<div class="container">
		<div class="row">
			<div class="col col-lg-4 mt-2 mb-2">
				<a class="footer-title-image" href="{{ url('/') }}">
          			<span class="icon icon-mm-logo"></span>
        		</a>	
			</div>
			<div class="col col-lg-8 mt-2 mb-2 pt-3">
				<div class="footer-links-block">
					<a class="footer-link active mr-4" target="_blank" href="{{ action('PDFController@termsAndConditions') }}">T&Cs</a>
					<a class="footer-link active mr-4" target="_blank" href="{{ action('PDFController@privacyPolicy') }}">Privacy Policy</a>
					<a class="footer-link active mr-4" target="_blank" href="{{ action('PDFController@conflictsOfInterestPolicy') }}">Conflict of interest</a>
					@if(Auth::check())
						<a class="footer-link active mr-4" target="_blank" href="{{ action('PDFController@tradingSpreads') }}">Trading Spreads &amp; Fees</a>
					@endif
					<a class="footer-link active" href="{{ action('PageController@fspDisclosures') }}">FSP Disclosures</a>
				</div>
			</div>
		</div>
	</div>
</footer>