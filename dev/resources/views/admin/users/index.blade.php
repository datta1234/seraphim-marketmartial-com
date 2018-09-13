@extends('layouts.canvas_app')

@section('content')
	<div class="container-fluid">
		<div class="card mt-2 mb-2">
  			<div class="card-body">
	  			<users-table user_data="{{ $userData }}">
	  				<a href="{{ route('register') }}" class="btn mm-generic-trade-button float-right">Create User</a>
	  			</users-table>
				<div class="row">
					<div class="col-2 offset-10 mt-2 mb-2">
						<button type="button" class="btn mm-generic-trade-button w-100">Download Log Files</button>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection