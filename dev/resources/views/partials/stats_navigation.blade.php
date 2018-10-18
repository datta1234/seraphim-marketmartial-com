<div class="card-group mt-4">
	<div class="card text-center stats-nav-card {{ Request::is('stats/my-activity') ? 'active' : '' }}">
		<a class="" href="{{ route('activity.show') }}">
			<div class="card-body">
				<h2><span class="icon icon-screen"></span></h2>
                <h5 class="front-title">
                    My Activity
                </h5>
			</div>
		</a>
    </div>
    <div class="card text-center stats-nav-card {{ Request::is('stats/market-activity') ? 'active' : '' }}">
		<a class="" href="{{ route('activity.index') }}">
			<div class="card-body">
				<h2><span class="icon icon-screen"></span></h2>
                <h5 class="front-title">
                    All Market Activity
                </h5>
			</div>
		</a>
    </div>
    <div class="card text-center stats-nav-card {{ Request::is('stats/open-interest') ? 'active' : '' }}">
		<a class="" href="{{ route('open_interest.show') }}">
			<div class="card-body">
				<h2><span class="icon icon-screen"></span></h2>
                <h5 class="front-title">
                    Open Interest
                </h5>
			</div>
		</a>
    </div>
</div>

@if(Auth::user()->role_id == 1)
	<upload-csv></upload-csv>
@endif