@extends('layouts.activity_log')

@section('content')
    <div class="container-fluid" id="activity_log_app">
        <div class="row">
            <div class="col-6 offset-3">
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
                        <activity-log-download :activity-types="activity_types" action="{{ route('admin.logging.download') }}" method="GET"></activity-log-download>
                    @endslot
                @endcomponent
            </div>
        </div>
    </div>
@endsection