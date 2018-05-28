<div class="form-group row">

	<div class="col-md-4">
		 {{ Form::label('email',$market->title, ['class' => 'col-form-label text-right']) }}

	</div>
	<div class="col-md-4">

		{{ Form::text("trading_accounts[{$index}][safex_number]",null,['class' => ($errors->has("trading_accounts.{$index}.safex_number") ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'safex member code']) }}
		{{ Form::hidden("trading_accounts[{$index}][market_id]",$market->id) }}

		@if ($errors->has("trading_accounts.{$index}.safex_number"))
		        <strong class="text-danger">{{ $errors->first("trading_accounts.{$index}.safex_number") }}</strong>
		@endif
	</div>
	<div class="col-md-4">

		{{ Form::text("trading_accounts[{$index}][sub_account]",null,['class' => ($errors->has("trading_accounts.{$index}.sub_account") ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'sub-account / allocation']) }}	

		@if ($errors->has("trading_accounts.{$index}.sub_account"))
		        <strong class="text-danger">{{ $errors->first("trading_accounts.{$index}.sub_account") }}</strong>
		@endif
	</div>

</div>