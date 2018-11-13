<div class="card mt-5">
@if($is_admin_update)
    <ul class="list-group list-group-flush mm-menu">
        <li class="list-group-item {{ Request::is('admin/user/'.$user->id.'/edit') ? 'active' : '' }}">
            <a class="user-nav-link nav-link  " href="{{ route('admin.user.edit',['user'=>$user->id]) }}">Profile</a>
        </li>
        <li class="list-group-item {{ Request::is('admin/user/email-settings/'.$user->id.'/edit') ? 'active' : '' }}">
            <a class="user-nav-link nav-link " href="{{ route('admin.user.email.edit',['user'=>$user->id]) }}">Emails</a>
        </li>
        <li class="list-group-item {{ Request::is('admin/user/trade-settings/'.$user->id.'/edit') ? 'active' : '' }}">
            <a class="user-nav-link nav-link" href="{{ route('admin.user.trade_settings.edit',['user'=>$user->id]) }}">Trade Settings</a>
        </li>
        <li class="list-group-item {{ Request::is('admin/user/interest-settings/'.$user->id.'/edit') ? 'active' : '' }}">
            <a class="user-nav-link nav-link" href="{{ route('admin.user.interest.edit',['user'=>$user->id]) }}">Interests</a>
        </li>
    </ul>  
@else 
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
@endif
</div>