{{--Alerts--}}
@if(Session::has('success'))
	<script type="text/javascript">
		Vue.toasted.success({!!json_encode(Session::get('success'))!!});
    </script>
@endif
@if(Session::has('info'))
	<script type="text/javascript">
		Vue.toasted.success({!!json_encode(Session::get('info'))!!});
    </script>
@endif
@if(Session::has('error'))
	<script type="text/javascript">
		Vue.toasted.success({!!json_encode(Session::get('error'))!!});
    </script>
@endif