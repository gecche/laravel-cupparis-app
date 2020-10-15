<!doctype html>
<html lang="{{ app()->getLocale() }}">
@include("includes.head")
<body >
    <div class="container-fluid">
        @include('includes.header')
        @yield('content')
    </div>
    @include('includes.footer')
    @yield('extra_scripts')
    @include('includes.inline-templates')
</body>
</html>