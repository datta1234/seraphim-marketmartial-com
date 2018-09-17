<nav id="mainNav" class="navbar navbar-default sticky-top">
    <div class="{{ ( isset($layout) && isset($layout['fluid']) && $layout['fluid'] ? 'container-fluid' : 'container' ) }}">
      	<div class="navbar-header">
      		@if(Auth::check())
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
	  			<li class="nav-item">
					<a class="nav-link active p-0 ml-4" href="{{ route('trade') }}">Trade</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active p-0 ml-4" href="{{ route('my_activity.show') }}">Stats</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active p-0 ml-4" href="#">Previous day</a>
				</li>
				@if(Auth::user()->role_id == 1)
					<li class="nav-item">
						<a class="nav-link active p-0 ml-4" href="{{ route('admin.user.index') }}">Users</a>
					</li>
				@else
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle p-0 ml-4" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">More</a>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="{{ route('user.edit') }}">My Profile</a>
							<a class="dropdown-item" href="{{ route('user.edit_password') }}">Change Password</a>
							<a class="dropdown-item" href="{{ route('email.edit') }}">Email Settings</a>
							<a class="dropdown-item" href="{{ route('trade_settings.edit') }}">Account Setting</a>
						</div>
					</li>
				@endif
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