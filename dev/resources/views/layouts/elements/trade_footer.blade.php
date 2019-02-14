<footer id="trade-footer">
	<div class="{{ ( isset($layout) && isset($layout['fluid']) && $layout['fluid'] ? 'container-fluid' : 'container' ) }}">
		<div class="row">
			<div class="col col-lg-4 mt-2 mb-2">
				<a class="footer-title-image" href="{{ url('/') }}">
          			<span class="icon icon-mm-logo-v2"></span>
          			<span class="footer-logo-text">All rights reserved @ 2018</span>
        		</a>	
			</div>
			<div class="col col-lg-8 mt-2 mb-2 pt-2">
				<div class="footer-links-block">
					<a class="footer-link active mr-4" target="_blank" href="{{ action('PDFController@termsAndConditions') }}">T&Cs</a>
					<a class="footer-link active mr-4" target="_blank" href="{{ action('PDFController@privacyPolicy') }}">Privacy Policy</a>
					<a class="footer-link active mr-4" target="_blank" href="{{ action('PDFController@conflictsOfInterestPolicy') }}">Conflict of interest</a>
					<a class="footer-link active" href="{{ action('PageController@fspDisclosures') }}">FSP Disclosures</a>
				</div>
			</div>
		</div>
	</div>
</footer>