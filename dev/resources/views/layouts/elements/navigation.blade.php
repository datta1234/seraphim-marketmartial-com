<nav id="mainNav" class="navbar navbar-default sticky-top">
    <div class="{{ ( isset($layout) && isset($layout['fluid']) && $layout['fluid'] ? 'container-fluid' : 'container' ) }}">
      	<div class="navbar-header">
        	<a class="navbar-brand nav-title-image" href="{{ url('/') }}">
				<span class="icon icon-mm-logo"></span>
        	</a>
      	</div>
  		<ul class="nav justify-content-end">
			@if(Auth::check())
	  			<li class="nav-item">
					<a class="nav-link active" href="{{ route('trade') }}">Trade</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" href="">Stats</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" href="">Previous day</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" href="">More</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">More</a>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="{{ route('user.edit') }}">My Profile</a>
						<a class="dropdown-item" href="{{ route('user.edit_password') }}">Change Password</a>
						<a class="dropdown-item" href="{{ route('email.edit') }}">Email Settings</a>
						<a class="dropdown-item" href="{{ route('account.edit') }}">Account Setting</a>
					</div>
				</li>
				<li class="nav-item">
					<a class="nav-link active" href="{{ route('logout') }}"
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
					<a class="nav-link active" href="{{ route('about') }}">About Us</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" href="{{ route('contact') }}">Contact Us</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" href="{{ route('register') }}">Sign up now</a>
				</li>
          	@endif
		</ul>
	</div>
</nav>