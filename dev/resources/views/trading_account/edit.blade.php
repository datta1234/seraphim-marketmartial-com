@extends('layouts.trade_app')

@section('content')
<div class="container">

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

            {!! Form::model($user,['route' => 'email.update']) !!}
				
				@foreach ($markets as $index => $market)
					@include('trading_account.partials.default', ['label' => $market,'index'=>$index])
				@endforeach
								
		


                    <div class="form-group row mb-0">

                	 <div class="col-sm-12 col-md-3 col-xl-2 mt-2">
                        <a class="btn mm-button float-right ml-2 w-100" href="#">Add E-mail</a>
                    </div>
                    <div class="col-sm-12 col-md-3 offset-md-6 col-xl-2 offset-xl-8 mt-2">
                        <button type="submit" class="btn mm-button float-right w-100">Update</button>
                    </div>
                   
                </div>

		            {!! Form::close() !!}


				@endslot
		@endcomponent
</div>
@endsection