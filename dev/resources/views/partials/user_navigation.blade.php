<div class="card mt-5">
  <div class="card-header" style="color:white;">
    User settings
  </div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item">
    	<a class="nav-link active" href="{{ route('user.edit') }}">My Profile</a>
    </li>
    <li class="list-group-item">
		<a class="nav-link active" href="{{ route('user.edit_password') }}">Change Password</a>
    </li>
    <li class="list-group-item">
		<a class="nav-link active" href="{{ route('email.edit') }}">Email Settings</a>
    </li>
    <li class="list-group-item">
		<a class="nav-link active" href="{{ route('account.edit') }}">Account Setting</a>
    </li>
  </ul>
</div>