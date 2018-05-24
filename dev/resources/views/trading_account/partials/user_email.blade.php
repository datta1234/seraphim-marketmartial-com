<div class="form-group row">
    {{ Form::label('email',$email->label, ['class' => 'col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right']) }}
<div class="col-sm-12 col-md-4">
  {{ Form::hidden("email[{$index}][id]",$email->id,['class' => ($errors->has('email.{$index}.email') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your email here...']) }}

  {{ Form::email("email[{$index}][email]",(old("email{$index}email") === null ?  $email->email : old("email{$index}email")),['class' => ($errors->has('email') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your email here...']) }}

@if($email->defaultLabel && $email->defaultLabel->id)
  {{ Form::hidden("email[{$index}][default_id]",$email->defaultLabel->id,['class' => ($errors->has('email.{$index}.id') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your email here...']) }}
@endif

  {{ Form::hidden("email[{$index}][notifiable]", $email->notifiable ,['class' => ($errors->has('email') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your email here...']) }}


	@if ($errors->has("email.{$index}.email"))
	    <strong class="text-danger">{{ $errors->first("email.{$index}.email") }}</strong>
	@endif

</div>
</div>