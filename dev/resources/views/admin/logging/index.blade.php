@extends('layouts.canvas_app')

@section('content')
    <div class="container-fluid">
        {{-- Logging Card --}}
        @component('partials.content_card')
            @slot('header')
                <h2 class="mt-2 mb-2">Activity Logs</h2>
            @endslot
            @slot('title')
            @endslot
            @slot('decorator')
            @endslot
            @slot('body')
                <div class="row">
                    
                </div>
                <div class="row">
                    <div class="col-2 offset-10 mt-2 mb-2">
                        <button type="button" class="btn mm-generic-trade-button w-100">Download Logs</button>
                    </div>
                </div>
            @endslot
        @endcomponent
    </div>
@endsection