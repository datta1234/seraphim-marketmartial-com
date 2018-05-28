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
					E-Mail Settings
				@endslot
			@slot('decorator')
				<hr class="title-decorator">
			@endslot
				@slot('body')

            {!! Form::model($user,['route' => 'email.update']) !!}
				
				@foreach ($defaultLabels as $index => $defaultLabel)
					@include('emails.partials.default', ['label' => $defaultLabel,'index'=>$index])
				@endforeach
								
				<!-- this is for if fields are alredy set -->
				@foreach ($emails as $index => $email)
					@include('emails.partials.user_email', ['email' => $email,'index'=> $defaultLabels->count() + $index])
				@endforeach


				<email-settings :email-settings-data="'{{ json_encode($emails) }}'" ></email-settings>


                    <div class="form-group row mb-0">

                	 <div class="col-3">
                        <a class="btn mm-button float-right ml-2 w-100" href="#">Add E-mail</a>
                    </div>
                    <div class="offset-6 col-3">
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