@extends('layouts.canvas_app')

@section('content')
	<div class="container-fluid">
		<h1>Profile of {{ $user->full_name }}</h1>
		<div class="row">
			<div class="col-6">
				{{-- Profile Details Card --}}
				{{--@component('partials.content_card')
				    @slot('header')
				        <h2 class="mt-1 mb-1"><span class="icon icon-fluid"></span></h2>
				    @endslot
				    @slot('title')
				        Profile Details
				    @endslot
				    @slot('decorator')
				        <hr class="title-decorator mm-info">
				    @endslot
				    @slot('body')   
				        details
				    @endslot
				@endcomponent--}}
				<div class="card mt-2 mb-2">
		  			<div class="card-body">
			  			<h2>Profile Details</h2>
			  			<div class="row">
			  				<div class="col col-lg-3">
			  					<p>Full Name:</p>
			  				</div>
			  				<div class="col col-lg-9">
			  					<p>{{ $user->full_name }}</p>
			  				</div>
			  			</div>
			  			<div class="row">
			  				<div class="col col-lg-3">
			  					<p>Work Number:</p>
			  				</div>
			  				<div class="col col-lg-9">
			  					<p>{{ $user->work_phone }}</p>
			  				</div>
			  			</div>
						<div class="row">
			  				<div class="col col-lg-3">
			  					<p>Cell Number:</p>
			  				</div>
			  				<div class="col col-lg-9">
			  					<p>{{ $user->cell_phone }}</p>
			  				</div>
			  			</div>
						<div class="row">
			  				<div class="col col-lg-3">
			  					<p>Email:</p>
			  				</div>
			  				<div class="col col-lg-9">
			  					<p>{{ $user->email }}</p>
			  				</div>
			  			</div>
						<div class="row">
			  				<div class="col col-lg-3">
			  					<p>Organisation:</p>
			  				</div>
			  				<div class="col col-lg-9">
			  					<p>{{ $user->organisation->title }}</p>
			  				</div>
			  			</div>
					</div>
				</div>
			</div>
			<div class="col-6">
				{{-- Email Settings Card --}}
				{{--@component('partials.content_card')
				    @slot('header')
				        <h2 class="mt-1 mb-1"><span class="icon icon-fluid"></span></h2>
				    @endslot
				    @slot('title')
				        Email Settings
				    @endslot
				    @slot('decorator')
				        <hr class="title-decorator mm-info">
				    @endslot
				    @slot('body')   
				        details
				    @endslot
				@endcomponent--}}
				<div class="card mt-2 mb-2">
		  			<div class="card-body">
			  			<h2>Email Settings</h2>
			  			<div class="row">
			  				@foreach($user->emails as $email)
				  				<div class="col col-lg-3">
				  					<p>{{ $email->title }}:</p>
				  				</div>
				  				<div class="col col-lg-9">
				  					<p>{{ $email->email }}</p>
				  				</div>
			  				@endforeach()
			  			</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				{{-- Trade Settings Card --}}
				{{--@component('partials.content_card')
				    @slot('header')
				        <h2 class="mt-1 mb-1"><span class="icon icon-fluid"></span></h2>
				    @endslot
				    @slot('title')
				        Trade Settings
				    @endslot
				    @slot('decorator')
				        <hr class="title-decorator mm-info">
				    @endslot
				    @slot('body')   
				        details
				    @endslot
				@endcomponent--}}
				<div class="card mt-2 mb-2">
		  			<div class="card-body">
			  			<h2>Trade Settings</h2>
			  			@foreach($user->tradingAccounts as $trading_account)
			  				<div class="row">
				  				<div class="col col-lg-3">
				  					<p>{{ $trading_account->market->title }}:</p>
				  				</div>
				  				<div class="col col-lg-3">
				  					<p>{{ $trading_account->safex_number }}</p>
				  				</div>
				  				<div class="col col-lg-3">
				  					<p>{{ $trading_account->sub_account }}</p>
				  				</div>
			  				</div>
		  				@endforeach()
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				{{-- Interests Card --}}
				{{--@component('partials.content_card')
				    @slot('header')
				        <h2 class="mt-1 mb-1"><span class="icon icon-fluid"></span></h2>
				    @endslot
				    @slot('title')
				        Interests
				    @endslot
				    @slot('decorator')
				        <hr class="title-decorator mm-info">
				    @endslot
				    @slot('body')   
				        details
				    @endslot
				@endcomponent --}}
				<div class="card mt-2 mb-2">
		  			<div class="card-body">
			  			<h2>Interests</h2>
			  			<div class="row">
			  				<div class="col col-lg-3">
			  					<p>Birthday:</p>
			  				</div>
			  				<div class="col col-lg-9">
			  					@datetime($user->birthdate)
			  				</div>
		  				</div>
		  				<div class="row">
			  				<div class="col col-lg-3">
			  					<p>Married:</p>
			  				</div>
			  				<div class="col col-lg-9">
			  					<p>{{ $user->is_married == 1 ? 'Yes' : 'No' }}</p>
			  				</div>
		  				</div>
		  				<div class="row">
			  				<div class="col col-lg-3">
			  					<p>Children:</p>
			  				</div>
			  				<div class="col col-lg-9">
			  					<p>{{ $user->has_children == 1 ? 'Yes' : 'No' }}</p>
			  				</div>
		  				</div>
			  			@foreach($user->interests as $interest)
			  				<div class="row">
				  				<div class="col col-lg-3">
				  					<p>{{ $interest->title }}:</p>
				  				</div>
				  				<div class="col col-lg-9">
				  					<p>{{ $interest->pivot->value }}</p>
				  				</div>
			  				</div>
		  				@endforeach()
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection