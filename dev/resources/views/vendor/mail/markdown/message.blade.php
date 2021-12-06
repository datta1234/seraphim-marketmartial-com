@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ isset($seraphim_mail) && ($seraphim_mail === true) ? 'SERAPHIM FINANCIAL SERVICES (PTY) LTD' : config('app.name') }}
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ isset($seraphim_mail) && ($seraphim_mail === true) ? '' : config('app.name').'. ' }}All rights reserved.
        @endcomponent
    @endslot
@endcomponent
