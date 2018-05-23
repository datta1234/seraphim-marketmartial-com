<nav id="mainNav" class="navbar navbar-default sticky-top">
    <div class="{{ ( isset($layout) && isset($layout['fluid']) && $layout['fluid'] ? 'container-fluid' : 'container' ) }}">
      	<div class="navbar-header">
        	<a class="navbar-brand nav-title-image" href="{{ url('/') }}">
				<span class="icon icon-mm-logo"></span>
        	</a>
      	</div>
  		<ul class="nav justify-content-end">
  			<li class="nav-item">
				<a class="nav-link active" href="{{ route('trade') }}">Trade</a>
			</li>
			<li class="nav-item">
				<a class="nav-link active" href="{{ route('about') }}">About Us</a>
			</li>
			<li class="nav-item">
				<a class="nav-link active" href="{{ route('contact') }}">Contact Us</a>
			</li>
			@if (Auth::guest())
				<li class="nav-item">
					<a class="nav-link active" href="{{ route('register') }}">Sign up now</a>
				</li>
			@endif
			@if(Auth::check())
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
          	@endif
		</ul>
	</div>
</nav>