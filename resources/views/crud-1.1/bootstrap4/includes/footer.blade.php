<div class="footer-dark text-center">
    <h4>Versione studio</h4>
</div>
<script>
    var app = null;
    jQuery( function() {
        crud.components.libs = {
            'csv-dashboard' : {
                js : '{!! Theme::url("components/js/csv-dashboard.js") !!}',
                tpl : '{!! Theme::url("components/templates/csv-dashboard.html") !!}',
            },
            'c-router' : {
                js : '{!! Theme::url("components/js/c-router.js") !!}',
            },
            'c-manage': {
                js  : '{!! Theme::url("components/js/c-manage.js") !!}',
                tpl : '{!! Theme::url("components/templates/c-manage.html") !!}',
            },
            'supplementari' : {
                js  : '{!! Theme::url("custom-components/supplementari.js") !!}',
            },
            {{--'c-wizard' : {--}}
            {{--    js  : '{!! Theme::url("components/js/c-wizard.js") !!}',--}}
            {{--    tpl : '{!! Theme::url("components/templates/c-wizard.html") !!}',--}}
            {{--},--}}
            {{--'c-drag-drop' : {--}}
            {{--    js  : '{!! Theme::url("components/js/c-drag-drop.js") !!}',--}}
            {{--    tpl : '{!! Theme::url("components/templates/c-drag-drop.html") !!}',--}}
            {{--},--}}
            {{--'v-menu' : {--}}
            {{--    js  : '{!! Theme::url("components/js/v-menu.js") !!}'--}}
            {{--}--}}
        },
        app = new CrudApp({
            data : {
            //         templatesFile : '/crud-vue/crud-vue.html',
            //         pluginsPath : '/bootstrap4/plugins/',
            //         el : '#app',
                appConfig : '/bootstrap4/CrudConf.js',
                componentsFiles : "{{Theme::url('crud-vue-components.js')}}"
            },
        });
    });
</script>

