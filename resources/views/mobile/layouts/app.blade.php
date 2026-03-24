@include('mobile.layouts.header')

<main>
    @yield('content')
</main>
@stack('scripts')
@include('mobile.layouts.footer')