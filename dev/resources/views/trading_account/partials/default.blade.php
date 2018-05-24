<div class="form-group row">

    {{ Form::label('email',$label->title, ['class' => 'col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right']) }}
<div class="col-sm-12 col-md-4">


  {{ Form::email("email[{$index}][email]",null,['class' => ($errors->has("email.{index}.email") ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your email here...']) }}
  {{ Form::hidden("email[{$index}][default_id]",$label->id,['class' => ($errors->has('email') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your email here...']) }}
  {{ Form::hidden("email[{$index}][notifiable]",1,['class' => ($errors->has('email') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your email here...']) }}
  
    @if ($errors->has("email.{index}.email"))
            <strong class="text-danger">{{ $errors->first("email.{index}.email") }}</strong>
    @endif
</div>
</div>