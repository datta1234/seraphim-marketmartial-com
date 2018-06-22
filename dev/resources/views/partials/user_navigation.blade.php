<div class="card mt-5">
  <ul class="list-group list-group-flush mm-menu">
    <li class="list-group-item {{ Request::is('my-profile') ? 'active' : '' }}">
    	<a class="user-nav-link nav-link  " href="{{ route('user.edit') }}">Profile</a>
    </li>
    <li class="list-group-item {{ Request::is('change-password') ? 'active' : '' }}">
		<a class="user-nav-link nav-link  " href="{{ route('user.edit_password') }}">Password</a>
    </li>
    <li class="list-group-item {{ Request::is('email-settings') ? 'active' : '' }}">
		<a class="user-nav-link nav-link " href="{{ route('email.edit') }}">Emails</a>
    </li>
    <li class="list-group-item {{ Request::is('trade-settings') ? 'active' : '' }}">
		  <a class="user-nav-link nav-link" href="{{ route('trade_settings.edit') }}">Trade Settings</a>
    </li>
    <li class="list-group-item {{ Request::is('interest-settings') ? 'active' : '' }}">
      <a class="user-nav-link nav-link" href="{{ route('interest.edit') }}">Interests</a>
    </li>
  </ul>
</div>