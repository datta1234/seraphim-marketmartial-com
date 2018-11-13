<div class="form-group row">

	<div class="col-md-4">
		 {{ Form::label('email',$trading_account->market->title, ['class' => 'col-form-label text-right']) }}

	</div>
	<div class="col-md-4">
		
		@if($trading_account->id)
		 {{ Form::hidden("trading_accounts[{$index}][id]",$trading_account->id) }}
 		@endif
 		{{ Form::hidden("trading_accounts[{$index}][market_id]",$trading_account->market_id) }}
		{{ Form::text("trading_accounts[{$index}][safex_number]",$trading_account->safex_number,['class' => ($errors->has("trading_accounts.{$index}.safex_number") ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'safex member code']) }}

		@if ($errors->has("trading_accounts.{$index}.safex_number"))
		        <strong class="text-danger">{{ $errors->first("trading_accounts.{$index}.safex_number") }}</strong>
		@endif
	</div>
	<div class="col-md-4">
		{{ Form::text("trading_accounts[{$index}][sub_account]",$trading_account->sub_account,['class' => ($errors->has("trading_accounts.{$index}.sub_account") ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'sub-account']) }}

		@if ($errors->has("trading_accounts.{$index}.sub_account"))
		        <strong class="text-danger">{{ $errors->first("trading_accounts.{$index}.sub_account") }}</strong>
		@endif
	</div>

</div>