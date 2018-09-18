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
		        <h2 class="mt-1 mb-1">Monthly Activity</span></h2>
		    @endslot
		    @slot('title')
		    @endslot
		    @slot('decorator')
		    @endslot
		    @slot('body')
		        <monthly-activity :market_data="{{ json_encode($graph_data) }}"></monthly-activity>
		    @endslot
		@endcomponent

		<div class="accordion" id="accordionExample">
			<div class="card mt-5">
				<div class="card-header text-center" id="headingOne">
					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<h2 class="mb-0">
							2018
						</h2>
					</button>
				</div>

				<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
					<div class="card-body">
						Table Here
					</div>
				</div>
			</div>
			<div class="card mt-5">
				<div class="card-header text-center" id="headingTwo">
					<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
						<h2 class="mb-0">
							2017
						</h2>
					</button>
				</div>
				<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
					<div class="card-body">
						Table Here
					</div>
				</div>
			</div>
			<div class="card mt-5">
				<div class="card-header text-center" id="headingThree">
					<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
						<h2 class="mb-0">
							2016
						</h2>
					</button>
				</div>
				<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
					<div class="card-body">
						Table Here
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection