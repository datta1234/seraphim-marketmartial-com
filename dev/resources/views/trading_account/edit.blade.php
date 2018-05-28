@extends('layouts.trade_app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-3">	
			@include('partials.user_navigation')
		</div>
		<div class="col-9">
			@component('partials.content_card')
			@slot('header')
			<h2 class="mt-1 mb-1"><span class="icon icon-addprofile"></span></h2>
			@endslot
				@slot('title')
					Trading Account Settings
				@endslot
			@slot('decorator')
				<hr class="title-decorator">
			@endslot
				@slot('body')

            {!! Form::model($user,['route' => 'account.edit']) !!}
				
				<div class="offset-1 col-md-10">
					@foreach ($markets as $index => $market)
						@include('trading_account.partials.default', ['market' => $market,'index'=>$index])
					@endforeach
					
					@foreach ($trading_accounts as $index => $trading_account)
						@include('trading_account.partials.trading_account', ['trading_account' => $trading_account,'index'=>$market->count() + $index])
					@endforeach


					<div class="col-md-3 offset-md-9">
					    <button type="submit" class="btn mm-button float-right w-100">Update</button>
					</div>

				</div>
		
		            {!! Form::close() !!}


				@endslot
		@endcomponent
		</div>
	</div>
		
</div>
@endsection