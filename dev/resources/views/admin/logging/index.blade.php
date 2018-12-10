@extends('layouts.activity_log')

@section('content')
    <div class="container-fluid" id="activity_log_app">
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
                <activity-log-modal :activity-types="activity_types" action="{{ route('admin.logging.download') }}" method="GET"></activity-log-modal>
            @endslot
        @endcomponent
    </div>
@endsection