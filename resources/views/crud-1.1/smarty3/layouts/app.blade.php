<!doctype html>
<html lang="{{ app()->getLocale() }}" xmlns="http://www.w3.org/1999/xhtml">
@include("includes.head")

    <!--
		Sticky/Reveal are not supported by this layout!
		****************************************************************************************************
			.layout-admin + .aside-focus + .layout-padded
		****************************************************************************************************
	-->
    <body class="layout-admin aside-sticky layout-padded">
        <div id="wrapper" class="d-flex align-items-stretch flex-column">
            @include('includes.header')
            <div id="wrapper_content" class="d-flex flex-fill">
                @include('includes.sidebar')

                <!-- MIDDLE -->
                <div id="middle" class="flex-fill">
                    @yield('content')
                </div>
                <!-- /MIDDLE -->
            </div>
            @include('includes.footer')
        </div>

        @if (env('JS_CDN',true))
            <script   src="https://code.jquery.com/jquery-3.5.1.min.js"
                      integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
                      crossorigin="anonymous">

            </script>
            <!-- Vue js -->
            <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
        @else
            {!! Theme::js('js/jquery-3.5.1.min.js')  !!}
            <!-- Vue js -->
            {!! Theme::js('js/vue.js')  !!}
        @endif
        <!-- crud-vue.js -->
        <script src="/crud-vue/crud-vue.js"></script>

        {!! Theme::js('js/it-translations.js')  !!}

        <!-- configurazione modelli -->
        {!! Theme::js('ModelConfs/ModelUser.js')  !!}

        <script>
            var app = null;
            jQuery( function() {
                crud.EventBus = new Vue();
                crud.layoutGradientColor = '{{$layoutGradientColor}}';
                crud.components.libs = {
                    'csv-dashboard' : {
                        js : '{!! Theme::url("components/js/csv-dashboard.js") !!}',
                        tpl : '{!! Theme::url("components/templates/csv-dashboard.htm") !!}',
                    },
                    'c-router' : {
                        js : '{!! Theme::url("components/js/c-router.j") !!}',
                    },
                    'c-manage': {
                        js  : '{!! Theme::url("components/js/c-manage.js") !!}',
                        tpl : '{!! Theme::url("components/templates/c-manage.htm") !!}',
                    },
                    'supplementari' : {
                        js  : '{!! Theme::url("custom-components/supplementari.js") !!}',
                    },
                    'c-wizard' : {
                        js  : '{!! Theme::url("components/js/c-wizard.js") !!}',
                        tpl : '{!! Theme::url("components/templates/c-wizard.htm") !!}',
                    },
                    'c-drag-drop' : {
                        js  : '{!! Theme::url("components/js/c-drag-drop.js") !!}',
                        tpl : '{!! Theme::url("components/templates/c-drag-drop.htm") !!}',
                    }
                },
                crud.routes['pages'] = {
                        url : '{!! Theme::url("pages") !!}/{path}',
                }
                app = new CrudApp({
                    data : {
                        templatesFiles : '{!! Theme::url("crud-vue.html") !!}',
                        //         pluginsPath : '/bootstrap4/plugins/',
                        el : '#wrapper',
                        appConfig : '{!! Theme::url("CrudConf.js") !!}',
                        componentsFiles : '{!! Theme::url("crud-vue-components.js") !!}',

                    },
                    mounted : function() {
                        var that = this;
                        that.loadResource('{!! Theme::url("assets/js/core.min.js") !!}')
                        //jQuery('#aside-main').find('.js-ajaxified').removeClass('js-ajaxified');
                        //$.SOW.reinit();
                    }
                });
            });
        </script>
        @yield('extra_scripts')
        @include('includes.inline-templates')
{{--        <script src="{!! Theme::url('assets/js/core.js') !!}"></script>--}}
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
