<div class="form-group row">

   
	<div class="col-md-4">
		 {{ Form::label('email',$label->title, ['class' => 'col-form-label text-right']) }}

	</div>
	<div class="col-md-4">

		{{ Form::email("email[{$index}][email]",null,['class' => ($errors->has("email.{index}.safex_number") ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'safex member code']) }}
		{{ Form::hidden("safex_number[{$index}][default_id]",$label->id,['class' => ($errors->has('safex_number') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'safex member code']) }}
		{{ Form::hidden("safex_number[{$index}][notifiable]",1,['class' => ($errors->has('safex_number') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'safex member code']) }}

		@if ($errors->has("safex_number.{index}.safex_number"))
		        <strong class="text-danger">{{ $errors->first("safex_number.{index}.safex_number") }}</strong>
		@endif
	</div>
	<div class="col-md-4">

		{{ Form::email("email[{$index}][email]",null,['class' => ($errors->has("email.{index}.sub_account") ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'sub-account / allocation']) }}
		{{ Form::hidden("sub_account[{$index}][default_id]",$label->id,['class' => ($errors->has('sub_account') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'sub-account / allocation']) }}
	

		@if ($errors->has("sub_account.{index}.sub_account"))
		        <strong class="text-danger">{{ $errors->first("sub_account.{index}.sub_account") }}</strong>
		@endif
	</div>

</div>