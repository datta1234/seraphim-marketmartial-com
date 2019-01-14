@extends('layouts.app')

@section('content')
{{-- Register Card --}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-addprofile"></span></h2>
    @endslot
    @slot('title')
    New Member Enquiry
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
        <div>
            <p class="card-text text-center">
            Please fill in your details below.<br>
            Once your credentials have been verified, you will be able to view and use the Market Martial trading platform.
            </p>

            <div class="row justify-content-md-center mb-">
                <div class="col col-sm-12 col-md-6 col-lg-5 col-xl-4">
                    <div class="row register-info-group justify-content-center" data-toggle="tooltip" data-trigger="click" data-placement="top" title="Security is extremely important to us and our users. We have therefore added a verification process to ensure that each user is legitimate. We always strive to achieve a fast turn-around time, so you should expect to hear back from us shortly.">
                        <div class="col-12 col-md-2 col-xl-1 text-center">
                            <span class="icon icon-info"></span>
                        </div>
                        <div class="col-auto register-info-text">
                            <p class="mb-1">Why do I need to wait for verification?</p>
                        </div>
                    </div>
                </div>
            </div>
            <br>

            {!! Form::open(['route' => 'register', 'id' => 'registerPageForm']) !!}
            

         <div class="form-group row">
           
                    {{ Form::label('email','E-Mail Address', ['class' => 'col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right']) }}
                <div class="col-sm-12 col-md-4">
                  {{ Form::email('email',null,['class' => ($errors->has('email') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your email here...']) }}


                    @if ($errors->has('email'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

             <div class="form-group row">
           
                    {{ Form::label('full_name','Full Name', ['class' => 'col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right']) }}
                <div class="col-sm-12 col-md-4">
                  {{ Form::text('full_name',null,['class' => ($errors->has('full_name') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your full name here...']) }}


                    @if ($errors->has('full_name'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('full_name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

               <div class="form-group row">
           
                    {{ Form::label('cell_phone','Phone', ['class' => 'col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right']) }}
                <div class="col-sm-12 col-md-4">
                  {{ Form::tel('cell_phone',null,['class' => ($errors->has('cell_phone') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Enter your phone number here...']) }}


                    @if ($errors->has('cell_phone'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('cell_phone') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

             <div class="form-group row">
                <label for="organisation_id" class="col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right">Your Organisation</label>

                <div class="col-sm-12 col-md-4">
                    <div class="form-group">

                      {{ Form::select('organisation_id', [''=>'Select Organisation'] + $organisations, null,['class' => ($errors->has('organisation_id') ? 'form-control is-invalid' : 'form-control'),'id'=>'organisation_id'] ) }}

                        @if ($errors->has('organisation_id'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('organisation_id') }}</strong>
                            </span>
                        @endif

                    </div>
                    
                    <div class="checkbox largeCheckBox">
                        <label>
                            {{ Form::checkbox('not_listed', 'not_listed',null,['data-not-listed-check'=>true]) }}
                            My organisation is not listed
                        </label>
                    </div>

                 {{ Form::hidden('new_organisation',null,['class' => ($errors->has('new_organisation') ? 'form-control is-invalid' : 'form-control'),'id'=>'new_organisation','placeholder'=>'Enter your organisation here...']) }}
                    
                    @if ($errors->has('new_organisation'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('new_organisation') }}</strong>
                        </span>
                    @endif
                </div>
            </div>


            <div class="form-group row">
                <label for="market_types" class="col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right">Markets that you will be trading</label>

                <div class="form-group col-sm-12 col-md-4 mt-2">
                    <div class="no-backdrop form-control {{ $errors->has('market_types') ? ' is-invalid' : '' }} mb-4" id="market_types">
                    @foreach ($market_types as $key=>$market)
                        <div class="form-check checkbox largeCheckBox">
                            <label lass="form-check-label">
                                <input class="form-check-input" @if( is_array(old('market_types')) && in_array($key,old('market_types'))) checked @endif name="market_types[]" type="checkbox" value="{{$key}}"> {{ $market }}
                            </label>
                        </div>
                    @endforeach
                    </div>

                    @if ($errors->has('market_types'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('market_types') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
         

      <div class="form-group row">
                <label for="organisation_id" class="col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right">Your Role</label>

                <div class="col-sm-12 col-md-4">
                    <div class="form-group">

                      {{ Form::select('role_id',[''=>'Select Role'] + $roles, null,['class' => ($errors->has('role_id') ? 'form-control is-invalid' : 'form-control')] ) }}

                        @if ($errors->has('role_id'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('role_id') }}</strong>
                            </span>
                        @endif

                    </div>
    
            </div>  
        </div>


            {{-- NEEDS CHANGE END --}}
            <div class="form-group row">
           
                    {{ Form::label('password','Password', ['class' => 'col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right']) }}
                <div class="col-sm-12 col-md-4">
                  {{ Form::password('password',['class' => ($errors->has('password') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Must be at least 8 characters long']) }}


                    @if ($errors->has('password'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
            </div>


            <div class="form-group row">
                  {{ Form::label('password_confirmation','Confirm Password', ['class' => 'col-sm-12 col-md-4 offset-md-1 col-form-label text-md-right']) }}

                <div class="col-sm-12 col-md-4">
                 {{ Form::password('password_confirmation',['class' => 'form-control','placeholder'=>'Repeat your password here...']) }}
                </div>
            </div>

            <div class="form-group row mb-0">
                @if(Auth::check())
                    <div class="col-sm-12 col-md-4 offset-md-2 col-xl-3 offset-xl-5 mt-2">
                        <input type="submit" value="Populate and Add" name="btn_populate" class="btn mm-button float-right w-100"/>
                    </div>
                    <div class="col-sm-12 col-md-3 col-xl-2 mt-2">
                        <input type="submit" value="Add User" name="btn_add" class="btn mm-button float-right w-100"/>
                    </div>
                @else
                    <div class="col-sm-12 col-md-3 offset-md-6 col-xl-2 offset-xl-8 mt-2">
                        <button type="submit" class="btn mm-button float-right w-100">Sign Me Up</button>
                    </div>
                @endif
                <div class="col-sm-12 col-md-3 col-xl-2 mt-2">
                    <a class="btn mm-button float-right ml-2 w-100" href="{{ url('/') }}">Cancel</a>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    @endslot
@endcomponent

@endsection

@section('footer-scripts')
    @parent
    <script type="text/javascript">
        function resetOrgSelect() {
            var org_check = $("#new_organisation");
            var org_drop = $("#organisation_id");

            if ($( "[data-not-listed-check]" ).prop('checked')) {
                org_drop.prop( "disabled", true );
                org_check.prop( "disabled", false );
                org_check.attr("type", "");
            } else {
                org_check.attr("type", "hidden");
                org_check.prop( "disabled", true );
                org_drop.prop( "disabled", false );
            }
        }
        $(document).ready(resetOrgSelect);
        /*
         * Register - Toggle organisation input state
         */
        $( "[data-not-listed-check]" ).on("change", resetOrgSelect);

        /*
        * Register - password and confirm match
        */
        var equality_check = false;
        var passElement = $( "#password" );
        var confirmElement = $( "#password_confirmation" );

        passElement.on( "input", function() {
            if ( confirmElement && $( this ) ) {
                if ( $( this ).val() == confirmElement.val() && $( this ).val() != '' ) {
                    //add class is-valid
                    confirmElement.removeClass("is-invalid");
                    confirmElement.addClass("is-valid");
                    equality_check = true;
                } else {
                    confirmElement.removeClass("is-valid");
                    confirmElement.addClass("is-invalid");
                    equality_check = false;
                }
            }
        });
        confirmElement.on( "input", function() {
            if (passElement && $( this )) {
                if ( $( this ).val() == passElement.val() && $( this ).val() != '' ) {
                    $( this ).removeClass("is-invalid");
                    $( this ).addClass("is-valid");
                    equality_check = true;
                } else {
                    $( this ).removeClass("is-valid");
                    $( this ).addClass("is-invalid");
                    equality_check = false;
                }
            }
        });
    </script>
@endsection