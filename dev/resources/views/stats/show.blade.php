@extends('layouts.canvas_app')

@section('content')
	<div class="container">
		<div class="card-group mt-4">
			<div class="card text-center">
				<a class="" href="{{ route('my_activity.show') }}">
					<div class="card-body">
						<h2><span class="icon icon-screen"></span></h2>
	                    <h5 class="front-title">
	                        My Activity
	                    </h5>
					</div>
				</a>
		    </div>
		    <div class="card text-center">
				<a class="" href="{{ route('my_activity.show') }}">
					<div class="card-body">
						<h2><span class="icon icon-screen"></span></h2>
	                    <h5 class="front-title">
	                        All Market Activity
	                    </h5>
					</div>
				</a>
		    </div>
		    <div class="card text-center">
				<a class="" href="{{ route('my_activity.show') }}">
					<div class="card-body">
						<h2><span class="icon icon-screen"></span></h2>
	                    <h5 class="front-title">
	                        Open Interest
	                    </h5>
					</div>
				</a>
		    </div>
		</div>

		{{-- Monthly Activity Card --}}
		@component('partials.content_card')
		    @slot('header')
		        <h2 class="mt-2 mb-2">Monthly Activity</span></h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')
		        <monthly-activity :market_data="{{ json_encode($graph_data) }}"></monthly-activity>
		    @endslot
		@endcomponent
		
		<my-activity-year-tables :years="{{ json_encode($years) }}"></my-activity-year-tables>
	</div>
@endsection