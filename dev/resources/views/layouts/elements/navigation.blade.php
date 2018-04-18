<nav id="mainNav" class="navbar navbar-default">
    <div class="container">
      	<div class="navbar-header">
        	<a class="navbar-brand" href="{{ url('/') }}">
          		<img class="main-title-image" src="">
        	</a>
      	</div>
  		<ul class="nav justify-content-end">
			<li class="nav-item">
				<a class="nav-link active" href="{{ route('about') }}">About Us</a>
			</li>
			<li class="nav-item">
				<a class="nav-link active" href="{{ route('contact') }}">Contact Us</a>
			</li>
			<li class="nav-item">
				<a class="nav-link active" href="{{ route('register') }}">Sign up now</a>
			</li>
		</ul>
	</div>
</nav>