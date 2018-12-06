<nav id="mainNav" class="navbar navbar-default sticky-top">
    <div class="{{ ( isset($layout) && isset($layout['fluid']) && $layout['fluid'] ? 'container-fluid' : 'container' ) }}">
      	<div class="navbar-header">
      		@if(Auth::check() && Auth::user()->verifiedActiveUser())
				<a class="navbar-brand nav-title-image" href="{{ route('trade') }}">
					<span class="icon icon-mm-logo"></span>
	        	</a>
      		@else
	        	<a class="navbar-brand nav-title-image" href="{{ url('/') }}">
					<span class="icon icon-mm-logo"></span>
	        	</a>
        	@endif
      	</div>
  		<ul class="nav justify-content-end">
			@if(Auth::check())
				@if(Auth::user()->verifiedActiveUser())
		  			<li class="nav-item">
						<a class="nav-link active p-0 ml-4" href="{{ route('trade') }}">Trade</a>
					</li>
					<li class="nav-item">
						<a class="nav-link active p-0 ml-4" href="{{ route('previous_day') }}">Previous day</a>
					</li>
				@endif
				@if(Auth::user()->role_id == 1)
					<li class="nav-item">
						<a class="nav-link active p-0 ml-4" href="{{ route('admin.activity.show') }}">Stats</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle p-0 ml-4" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Management</a>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="{{ route('admin.user.index') }}">
								Users
							</a>
							<a class="dropdown-item" href="{{ route('admin.markets.index') }}">
								Markets
							</a>
							<a class="dropdown-item" href="{{ route('admin.rebates.index') }}">
								Rebates
							</a>
							<a class="dropdown-item" href="{{ route('admin.booked-trades.index') }}">
								Booked Trades
							</a>
							<a class="dropdown-item" href="{{ route('admin.mfa.setup') }}">
								Setup MFA
							</a>
						</div>
					</li>
					<li class="nav-item">
						<a class="nav-link active p-0 ml-4" href="{{ route('admin.rebate_summary.index') }}">
							Rebates Summary
						</a>
					</li>
				@else
					@if(Auth::user()->verifiedActiveUser())
						<li class="nav-item">
							<a class="nav-link active p-0 ml-4" href="{{ route('activity.show') }}">Stats</a>
						</li>
					@endif
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle p-0 ml-4" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">More</a>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="{{ route('user.edit') }}">My Profile</a>
							<a class="dropdown-item" href="{{ route('user.edit_password') }}">Change Password</a>
							<a class="dropdown-item" href="{{ route('email.edit') }}">Email Settings</a>
							<a class="dropdown-item" href="{{ route('trade_settings.edit') }}">Trade Settings</a>
							@if(Auth::user()->verifiedActiveUser())
								<a class="dropdown-item" href="{{ route('rebate_summary.index') }}">
									Rebates Summary
								</a>
							@endif
						</div>
					</li>
				@endif
				@impersonating
						<li class="nav-item">
							<a class="nav-link active p-0 ml-4" href="{{ route('impersonate.leave') }}">
								Leave Impersonation
							</a>
						</li>						
				@endImpersonating
				<li class="nav-item">
					<a class="nav-link active p-0 ml-4" href="{{ route('logout') }}"
					onclick="event.preventDefault();
					document.getElementById('logout-form').submit();">
					Logout
					</a>

		            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
		              {{ csrf_field() }}
		            </form>
	          	</li>
	        @else
				<li class="nav-item">
					<a class="nav-link active p-0 ml-4" href="{{ route('about') }}">About Us</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active p-0 ml-4" href="{{ route('contact') }}">Contact Us</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active p-0 ml-4" href="{{ route('register') }}">Sign up now</a>
				</li>
          	@endif
		</ul>
	</div>
</nav>