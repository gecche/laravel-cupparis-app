<!doctype html>
<html lang="{{ app()->getLocale() }}" xmlns="http://www.w3.org/1999/xhtml">
@include("includes.head")

<!--
		Sticky/Reveal are not supported by this layout!
		****************************************************************************************************
			.layout-admin + .aside-focus + .layout-padded
		****************************************************************************************************
	-->
<body>
<div id="wrapper">

    <!-- light logo -->
    <a aria-label="go back" href="/"
       class="espad-main position-absolute top-0 start-0 my-2 mx-4 z-index-3 h--60 d-inline-flex align-items-center bg-white full-width">
                    <span data-gfont="Sriracha" class="fs--25 text-dark">
                        <img src="{!! Theme::url('images/ifc.png') !!}" width="55" height="40"
                             alt="CNR-IFC Progetto Abruzzo">
                    </span>
        {{--                <img src="{{Theme::url('assets/images/logo/logo_dark.svg')}}" width="110" alt="...">--}}
    </a>


    <div class="d-lg-flex text-white h--70 bg-white">

    </div>
    <div class="d-lg-flex text-white min-h-100vh {{$layoutGradientColor}}">

        @yield('content')

    </div>

{{--    <div class="d-lg-flex text-dark h--100 bg-white">--}}
{{--    </div>--}}
</div>

<script src="{!! Theme::url("assets/js/core.min.js") !!}"></script>

<!--

    [SOW Ajax Navigation Plugin] [AJAX ONLY, IF USED]
    If you have specific page js files, wrap them inside #page_js_files
    Ajax Navigation will use them for this page!
    This way you can load this page in a normal way and/or via ajax.
    (you can change/add more containers in sow.config.js)

    +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    NOTE: This is mostly for frontend, full ajax navigation!
    Admin Panels use a backend, so the content should be served without
    menu, header, etc! Else, the ajax has no reason to be used because will
    not minimize server load!

    /documentation/plugins-sow-ajax-navigation.html
    +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

-->
<div id="page_js_files"><!-- specific page javascript files here -->


</div>
</body>
</html>
