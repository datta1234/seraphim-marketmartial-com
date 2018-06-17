
@foreach ($emails as $key=>$email)
	<div class="form-group row">
		<div class="col-md-5">			
			{{ Form::email("email[{$key}][email]",($email->title) ? $email->title : $email->defaultLabel->title,['class' =>  'form-control','disabled'=>true]) }}
		</div>
		<div class="col-md-5">	
			{{ Form::email("email[{$key}][email]",$email->email,['disabled'=>true,'class' =>  'form-control']) }}		
		</div>
		<div class="col-md-2">	
			{{ Form::hidden("email[{$key}][id]", $email->id ) }}
			{{ Form::hidden("email[{$key}][notifiable]", false) }}
			@if(in_array($email->title,config('marketmartial.AutoSetTradeAccounts')))
				{{ Form::checkbox("email[{$key}][notifiable]",true, true,['disabled'=>true]) }}
			@else
				{{ Form::checkbox("email[{$key}][notifiable]",true, $email->notifiable) }}
			@endif
		</div>
	</div>
@endforeach
