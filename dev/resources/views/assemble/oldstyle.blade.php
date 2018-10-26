@extends('layouts.app')

@section('content')

<div class="home-login-block">
<div class="row">
    <div class="col col-lg-8">
    <div class="home-page-title">
        <h1>The Inter-Bank Derivatives<br>Trading Platform</h1>
    </div>
    </div>
    @if (Auth::guest())
    <div class="col col-lg-4">
    <div class="home-login float-md-right">
        <form id="homePageLoginForm" method="POST" action="{{ route('login') }}">
             {{ csrf_field() }}

                <div class="w-100 mb-3">
                    <div class="in-line-input email-input">
                        
                        <input id="email" type="email" class="form-control pb-0 {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="Email address" required autofocus>
                    </div>

                    @if ($errors->has('email'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="w-100 mb-3">
                    <div class="in-line-input pass-input">
                        
                        <input id="password" type="password" class="form-control pb-0 {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" required>
                    </div>

                    @if ($errors->has('password'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="w-100 mb-3">
                    <button type="submit" class="btn mm-login-button w-100 pb-1 pt-1">
                        {{ __('Login') }}
                    </button>
                </div>
                <div class="m-0">
                    <a class="btn btn-link m-0 p-0" href="{{ route('password.request') }}">
                        {{ __('Forgot Password') }}
                    </a>
                </div>
        </form>
    </div>
    </div>
    @endif
</div>
</div>

{{-- Table --}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-award"></span></h2>
    @endslot
    @slot('title')
        Sample Table
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    
                    <table class="table table-bordered">
                      <thead class="thead-dark">
                        <tr class="table-primary">
                          <th scope="col">Type</th>
                          <th scope="col">Column heading</th>
                          <th scope="col">Column heading</th>
                          <th scope="col">Column heading</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <th scope="row">Default</th>
                          <td>Column content</td>
                          <td><a href="#">Link</a></td>
                          <td>Column content</td>
                        </tr>
                        <tr>
                          <th scope="row">Default</th>
                          <td>Column content</td>
                          <td><a href="#">Link</a></td>
                          <td>Column content</td>
                        </tr>
                        <tr class="table-primary">
                          <th scope="row">Primary</th>
                          <td>Column content</td>
                          <td><a href="#">Link</a></td>
                          <td>Column content</td>
                        </tr>
                        <tr class="table-secondary">
                          <th scope="row">Secondary</th>
                          <td>Column content</td>
                          <td><a href="#">Link</a></td>
                          <td>Column content</td>
                        </tr>
                        <tr class="table-success">
                          <th scope="row">Success</th>
                          <td>Column content</td>
                          <td><a href="#">Link</a></td>
                          <td>Column content</td>
                        </tr>
                        <tr class="table-danger">
                          <th scope="row">Danger</th>
                          <td>Column content</td>
                          <td><a href="#">Link</a></td>
                          <td>Column content</td>
                        </tr>
                        <tr class="table-warning">
                          <th scope="row">Warning</th>
                          <td>Column content</td>
                          <td><a href="#">Link</a></td>
                          <td>Column content</td>
                        </tr>
                        <tr class="table-info">
                          <th scope="row">Info</th>
                          <td>Column content</td>
                          <td><a href="#">Link</a></td>
                          <td>Column content</td>
                        </tr>
                        <tr class="table-light">
                          <th scope="row">Light</th>
                          <td>Column content</td>
                          <td><a href="#">Link</a></td>
                          <td>Column content</td>
                        </tr>
                        <tr class="table-dark">
                          <th scope="row">Dark</th>
                          <td>Column content</td>
                          <td><a href="#">Link</a></td>
                          <td>Column content</td>
                        </tr>
                      </tbody>
                    </table> 
                </div>
            </div>
    @endslot
@endcomponent
{{-- End Table --}}


{{-- Progress Bar --}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-award"></span></h2>
    @endslot
    @slot('title')
        Sample Progress Bar
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="progress border">
                      <div class="progress-bar bg-primary" style="width:34%">Loading 34%</div>
                    </div>
                </div>
            </div>
    @endslot
@endcomponent
{{-- End Progress Bar --}}

{{-- Headless  card--}}
@component('partials.content_card_headless')
    @slot('title')
        Sample Headless Card
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <p>Phasellus augue sapien, ultricies non erat sit amet, facilisis tincidunt ex. Curabitur suscipit, ante et mattis varius, est lorem interdum nisl, eu fermentum ligula tortor vitae ipsum. Duis lacinia nulla sed sollicitudin molestie. Vivamus in pellentesque est. Duis id consectetur dui. Mauris lobortis at metus scelerisque rhoncus. Donec eu urna non nisi aliquet volutpat ut at justo. Quisque ultrices dolor sed metus varius, bibendum aliquet neque sodales. Duis eu nisl maximus, luctus mauris quis, blandit magna. Donec mollis, orci vel finibus rutrum, ante nisi mollis nisi, at tincidunt est orci in augue. Aliquam sit amet volutpat augue. </p>
                </div>
            </div>
    @endslot
@endcomponent
{{-- End Headless card --}}

{{-- Tabs - Cards --}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-award"></span></h2>
    @endslot
    @slot('title')
        Sample Tabs Using Cards
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <template>
                        <div dusk="monthly-activity" class="monthly-activity" >
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-primary mr-1">Selected</button>
                                <button type="button" class="btn btn-secondary mr-1">Unselected</button>
                                <button type="button" class="btn btn-secondary mr-1">Unselected</button>
                            </div>
                            <div class="card graph-card">
                                <div class="card-body">
                                    <bar-graph :data="active_data_set" :options="options"></bar-graph>
                                    <b-form-checkbox class="float-right mt-3" @change="toggleMyTrades" v-model="my_trades_only">
                                        Show only my trades
                                    </b-form-checkbox>
                                </div>
                                <div v-else class="card-body">
                                    <p class="text-center">No Data for this market to display</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
    @endslot
@endcomponent
{{-- End Tabs- cards --}}

{{-- Tabs - Nav --}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-award"></span></h2>
    @endslot
    @slot('title')
        Sample Tabs Using Nav
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <ul class="nav nav-tabs">
                      <li class="nav-item">
                        <a class="nav-link bg-primary mr-1 active" href="#">Selected</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link bg-secondary mr-1" href="#">Unselected</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link bg-secondary mr-1" href="#">Unselected</a>
                      </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card graph-card">
                        <div class="card-body">
                            <bar-graph :data="active_data_set" :options="options"></bar-graph>
                            <b-form-checkbox class="float-right mt-3" @change="toggleMyTrades" v-model="my_trades_only">
                                Show only my trades
                            </b-form-checkbox>
                        </div>
                        <div v-else class="card-body">
                            <p class="text-center">No Data for this market to display</p>
                        </div>
                    </div>
                </div>
            </div>
    @endslot
@endcomponent
{{-- End Tabs- Nav --}}

{{-- Card - List Group--}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-award"></span></h2>
    @endslot
    @slot('title')
        Sample Card with List Group
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
         <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card mt-2">
                    <h4 class="card-header">
                        Heading
                    </h4>
                  <ul class="list-group list-group-flush mm-menu">
                    <li class="list-group-item ">
                        <a class="user-nav-link nav-link  " href="#">Profile</a>
                    </li>
                    <li class="list-group-item active">
                        <a class="user-nav-link nav-link  " href="#">Active</a>
                    </li>
                    <li class="list-group-item ">
                        <a class="user-nav-link nav-link " href="#">Emails</a>
                    </li>
                    <li class="list-group-item">
                          <a class="user-nav-link nav-link" href="#">Trade Settings</a>
                    </li>
                    <li class="list-group-item ">
                      <a class="user-nav-link nav-link" href="#">Interests</a>
                    </li>
                  </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card mt-5">
                  <ul class="list-group list-group-flush mm-menu">
                    <li class="list-group-item ">
                        <a class="user-nav-link nav-link  " href="#">Profile</a>
                    </li>
                    <li class="list-group-item ">
                        <a class="user-nav-link nav-link  " href="#">Password</a>
                    </li>
                    <li class="list-group-item ">
                        <a class="user-nav-link nav-link " href="#">Emails</a>
                    </li>
                    <li class="list-group-item active">
                          <a class="user-nav-link nav-link" href="#">Trade Settings</a>
                    </li>
                    <li class="list-group-item ">
                      <a class="user-nav-link nav-link" href="#">Interests</a>
                    </li>
                  </ul>
                </div>
            </div>
        </div>
    @endslot
@endcomponent
{{-- Card - List Group --}}

{{-- Sample Well--}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-award"></span></h2>
    @endslot
    @slot('title')
        Sample Well
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="well">
                        This is some text within a card body.
                    </div>
                </div>
            </div>
    @endslot
@endcomponent
{{-- End Sample Well--}}

{{-- Sample Container--}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-award"></span></h2>
    @endslot
    @slot('title')
        Sample Container
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="mm-card card text-white bg-primary mb-3 mr-auto ml-auto" style="max-width: 35rem;">
                      <h4 class="card-header text-center">Confirm Market Request</h4>
                      <div class="card-body">
                        <p class="card-text text-center">Sed blandit pharetra dignissim. Cras non diam non dolor placerat mattis consequat nec lorem. Nam tempor posuere dui sed congue. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent semper nulla at erat dignissim, sit amet placerat velit ullamcorper.</p>
                      </div>
                    </div>
                </div>
            </div>
    @endslot
@endcomponent
{{-- End Sample Container--}}

{{-- Info-block --}}
<div class="info-block mt-5">
    <div class="card-group flip-card-group">
    	{{-- flip-container --}}
        <div class="flip-container card text-center" ontouchstart="this.classList.toggle('hover');">
            <div class="flipper">
                <div class="front card-screen pt-3">
                    <h2><span class="icon icon-screen"></span></h2>
                    <h5 class="front-title text-center">
                        All markets on one screen
                    </h5>
                </div>
                <div class="back card-screen">
                    <h5 class="back-title text-center pl-2 pr-2">
                        Personalise the trading screen to see all markets that are relevant to you
                    </h5>
                </div>
            </div>
        </div>
        {{-- End flip-container--}}
        {{-- flip-container --}}
        <div class="flip-container card text-center" ontouchstart="this.classList.toggle('hover');">
            <div class="flipper">
                <div class="front card-award pt-3">
                    <h2><span class="icon icon-award2"></span></h2>
                    <h5 class="front-title text-center">
                        Get rewarded for market<br>making
                    </h5>
                </div>
                <div class="back card-award">
                    <h5 class="back-title text-center pl-2 pr-2">
                        To improve liquidity, initial market makers are incentivised by receiving rebates, irrespective of whether the market maker trades
                    </h5>
                </div>
            </div>
        </div>
        {{-- End flip-container --}}
        {{-- flip-container --}}
        <div class="flip-container card text-center" ontouchstart="this.classList.toggle('hover');">
            <div class="flipper">
                <div class="front card-graph pt-3">
                    <h2><span class="icon icon-graph"></span></h2>
                    <h5 class="front-title text-center">
                        Personalised trading stats
                    </h5>
                </div>
                <div class="back card-graph">
                    <h5 class="back-title text-center pl-2 pr-2">
                        Sort, filter and view both incomplete (doubles) and completed market trades as well as individual activity stats: trades, markets made, rebates, market share…
                    </h5>
                </div>
            </div>
        </div>
        {{-- End flip-container --}}
        {{-- flip-container --}}
        <div class="flip-container card text-center" ontouchstart="this.classList.toggle('hover');">
            <div class="flipper">
                <div class="front card-robot pt-3">
                    <h2><span class="icon icon-robot"></span></h2>
                    <h5 class="front-title text-center">
                        Automated bookings
                    </h5>
                </div>
                <div class="back card-robot">
                    <h5 class="back-title text-center pl-2 pr-2">
                        Upon acceptance of automated confirmations, trades are instantaneously booked to pre-selected trading accounts, removing delays and human error
                    </h5>
                </div>
            </div>
        </div>
        {{-- End flip-container --}}
    </div>

    <div class="card text-center">
      <div class="card-body">
        <h2 class="info-card">
            <span class="icon icon-info"></span>
        </h2>

        <h2 class="card-title text-center">
            What does Market Martial do?
        </h2>
        <hr class="title-decorator mm-info">
        <div class="row justify-content-md-center">
            <div class="col col-lg-10">
                <p class="card-text text-center">
                    Market Martial has been custom built exclusively for inter-bank derivatives traders with the intention of providing a seamless transition to an efficient electronic platform.
                </p>  
            </div>
        </div>
      </div>
    </div>
</div>
{{-- End Info-block --}}

{{-- Electronic efficiency Card --}}
@component('partials.content_card')
    @slot('header')
        <h2 class="mt-1 mb-1"><span class="icon icon-award"></span></h2>
    @endslot
    @slot('title')
        Electronic efficiency
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
            <div class="row">
                <div class="col-md-9 offset-md-3">
                    
                    <ul class="elec-eff-card-list">
                        <li>All markets in one place, on one screen.</li>
                        <li>No broker bias.</li>
                        <li>Automated execution, confirmation and booking.</li>
                        <li>Personalised activity stats.</li>
                        <li>Record of market trades, including market doubles (incomplete trades).</li>
                        <li>Secure and anonymous trading.</li>
                    </ul>
                </div>
            </div>
    @endslot
@endcomponent
{{-- End Electronic efficiency Card --}}

{{-- Priority Card --}}
@component('partials.content_card')
    @slot('header')
        <h2><span class="icon icon-question"></span></h2>
    @endslot
    @slot('title')
        Why Us?
    @endslot
    @slot('decorator')
        <hr class="title-decorator">
    @endslot
    @slot('body')
        <div class="row why-us-card justify-content-md-center">
            <div class="col-md-3 circle-icon-tile text-center">
                <div class="circle-icon-block m-3"><span class="icon icon-business p-4"></span></div>
                <p class="mt-3 pt-3">
                    Experienced<br>specialist support
                </p>
            </div>
            <div class="col-md-2 circle-icon-tile text-center">
            <div class="circle-icon-block m-3"><span class="icon icon-wifi2 p-4"></span></div>
                <p class="mt-3 pt-3">
                    Advanced online<br>functionality
                </p>
            </div>
            <div class="col-md-3 circle-icon-tile text-center">
                <div class="circle-icon-block m-3"><span class="icon icon-money2 p-4"></span></div>
                <p class="mt-3 pt-3">
                    Rewards for market<br>making
                </p>
            </div>
        </div>
    @endslot
@endcomponent
{{-- End Priority Card --}}

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
            Fill in your details below and we will email you a login to complete your profile.<br>
            Once your credentials have been verified you will be able to view and use the Market Martial trading platform.
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
                <div class="col-sm-12 col-md-3 offset-md-6 col-xl-2 offset-xl-8 mt-2">
                    <button type="submit" class="btn mm-button float-right w-100">Sign Me Up</button>
                </div>
                <div class="col-sm-12 col-md-3 col-xl-2 mt-2">
                    <a class="btn mm-button float-right ml-2 w-100" href="{{ url('/') }}">Cancel</a>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    @endslot
@endcomponent
{{-- End Register Card --}}

{{-- Sign up now button --}}
<div class="row justify-content-md-center">
    <div class="signup-block col col-lg-4 col-md-6  col-sm-12">
        <a class="btn mm-login-button w-100" href="{{ route('register') }}">Sign up now</a>
    </div>
</div>
{{-- End Sign up now button --}}

<div class="container">
	<div class="row">
		<div class="col-3">	
			@include('partials.user_navigation')
		</div>
		<div class="col-9">
			@component('partials.content_card')
			@slot('header')
			<h2 class="mt-1 mb-1">Tell Us More About Yourself</h2>
			@endslot
			
				@slot('body')
					
		<div class="row">
			<div class="col-md-8 offset-md-2">

				<div class="form-group row">

			        {{ Form::label('birthdate','Birthdate', ['class' => 'col-sm-4 col-form-label']) }}
				    <div class="col-sm-8">
						<day-month-picker name="birthdate" value="1990" ></day-month-picker>
				        @if ($errors->has('birthdate'))
				            <span class="text-danger">
				                <strong>{{ $errors->first('birthdate') }}</strong>
				            </span>
				        @endif
				    </div>
				</div>
				<div class="form-group row">
					
					<div class="col-md-6">
						{{ Form::label('email', 'Are you married?') }}
						
						<div class="row">
							<div class="col-md-12">
									<div class="form-check form-check-inline">
						 {{ Form::radio('is_married',1,null,['id'=>'is_married_yes','class'=>'form-check-input']) }}
						  <label class="form-check-label" for="is_married_yes">
						   	Yes
						  </label>
						</div>
						<div class="form-check form-check-inline">
						 {{ Form::radio('is_married',0,null,['id'=>'is_married_no','class'=>'form-check-input']) }}
						  <label class="form-check-label" for="is_married_no">
						    No
						  </label>
						</div>
							</div>
						</div>
						@if ($errors->has("is_married"))
								<span class="text-danger">
									<strong>{{ $errors->first("is_married") }}</strong>
								</span>
							@endif
					</div>

					<div class="col-md-6">
						{{ Form::label('has_children', 'Do you have children?') }}

						<div class="row">
							<div class="col-md-12">
								<div class="form-check form-check-inline">
								 {{ Form::radio('has_children',0,null,['id'=>'has_children_yes','class'=>'form-check-input']) }}
								  <label class="form-check-label" for="has_children_yes">
								   	Yes
								  </label>
								</div>
								<div class="form-check form-check-inline">
								 {{ Form::radio('has_children',1,null,['id'=>'has_children_no','class'=>'form-check-input']) }}
								  <label class="form-check-label" for="has_children_no">
								    No
								  </label>
								</div>
							</div>
						</div>
							@if ($errors->has("has_children"))
								<span class="text-danger">
									<strong>{{ $errors->first("has_children") }}</strong>
								</span>
							@endif

					</div>


				</div>
					

				<div class="form-group row">
				    <div class="col-sm-12">
			        	{{ Form::label('hobbies') }}
				      {{ Form::textArea('hobbies',null,['class' => ($errors->has('hobbies') ? 'form-control is-invalid' : 'form-control'),'placeholder'=>'Hobbies']) }}


				        @if ($errors->has('hobbies'))
				            <span class="invalid-feedback">
				                <strong>{{ $errors->first('hobbies') }}</strong>
				            </span>
				        @endif
				    </div>
				</div>

				<div class="form-group row mb-0">
					<div class="col-md-12">
                    <button type="submit" class="btn mm-button float-right w-100">Submit</button>
                </div>
					</div>
				</div>



	            {!! Form::close() !!}

		</div>
	</div>
				@endslot
		@endcomponent
		</div>
	</div>
		
</div>

@endsection




{{-- Sub-footer --}}
@section('sub-footer')

<div class="home-sub-footer">
    <div class="container">
        <div class="row">
            <div class="col col-12 col-md-6 col-lg-4 offset-lg-1 footer-sections pt-3 pb-3">
                <h3>Send us a message</h3>
                <form id="ContactUsForm" action="{{ route('contact') }}" method="POST">
                     {{ csrf_field() }}
                    <div class="form-group mb-2">
                        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" id="name" name="name" placeholder="Enter your name here...">

                        @if ($errors->has('name'))
                            <div class="alert alert-danger">
                                <strong>{{ $errors->first('name') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-2">
                        <input type="text" class="form-control {{ $errors->has('contact_email') ? ' is-invalid' : '' }}" value="{{ old('contact_email') }}" id="contactEmail" name="contact_email" placeholder="Enter your email here...">
                        @if ($errors->has('contact_email'))
                            <div class="alert alert-danger">
                                <strong>{{ $errors->first('contact_email') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-2">
                        <textarea class="form-control {{ $errors->has('contact_message') ? ' is-invalid' : '' }}" id="contactMessage" name="contact_message" rows="10" placeholder="Enter your message here...">{{ old('contact_message') }}</textarea>
                        @if ($errors->has('contact_message'))
                            <div class="alert alert-danger">
                                <strong>{{ $errors->first('contact_message') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="form-group mb-2">
                        <button disabled type="submit" class="btn mm-footer-button float-right">Submit message</button>
                    </div>
                </form>
            </div>
            <!-- <div class="col col-lg-4 footer-sections">
                <h3>About Market Martial</h3>
                <p>
                    With as many brokers as there are banks, the question has always stood: How does a broker create edge, or how does a broker provide a service that truly is superior to all the rest?
                </p>
                <p>
                    After 7 years of managing Investec Bank’s Index and Single Stock Options trading books, with the necessary experience and expertise gained in this very niche market, I believe Market Martial is the answer to that question, and more.
                </p>
            </div> -->
            <div class="col col-12 col-md-6 col-lg-5 offset-lg-2 benefits-block pt-3 pb-3">
                <h3>Benefits of using Market Martial</h3>
                <div class="row">
                    <div class="col col-2 col-lg-1 col-md-2">
                        <span class="icon icon-money2"></span>
                    </div>
                    <div class="col col-10 col-lg-11 col-md-10">
                        <p class="mb-3">get paid to make markets and trade!</p>
                    </div>

                    <div class="col col-2 col-lg-1 col-md-2">
                        <span class="icon icon-globe"></span>
                    </div>
                    <div class="col col-10 col-lg-11 col-md-10">
                        <p class="mb-3">easy to view markets, from anywhere in the world.</p>
                    </div>

                    <div class="col col-2 col-lg-1 col-md-2">
                        <span class="icon icon-graph"></span>
                    </div>
                    <div class="col col-10 col-lg-11 col-md-10">
                        <p class="mb-3">trade stats and market info at your fingertips.</p>
                    </div>

                    <div class="col col-2 col-lg-1 col-md-2">
                        <span class="icon icon-man"></span>
                    </div>
                    <div class="col col-10 col-lg-11 col-md-10">
                        <p class="mb-3">become a game-changer.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection