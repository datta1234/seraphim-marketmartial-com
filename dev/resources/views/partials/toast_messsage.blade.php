{{--Alerts--}}
@if(Session::has('success'))
	<script type="text/javascript">
		Vue.toasted.success({!!json_encode(Session::get('success'))!!});
    </script>
@endif
@if(Session::has('info'))
	<script type="text/javascript">
		Vue.toasted.info({!!json_encode(Session::get('info'))!!});
    </script>
@endif
@if(Session::has('error'))
	<script type="text/javascript">
		Vue.toasted.error({!!json_encode(Session::get('error'))!!});
    </script>
@endif
@if(Session::has('market_errors'))
	<script type="text/javascript">
		@foreach(Session::get('market_errors') as $market_error)
			Vue.toasted.error({!! json_encode($market_error) !!});
		@endforeach
    </script>
@endif