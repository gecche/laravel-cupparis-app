@extends('app')
@section('content')


    <div class="row">
        <div id="app" class="col col-sm-6">
            <c-router ref="menu" c-default-command="c-test?cComponent=r-input" c-content-id="#pop"></c-router>
            <div id="pop"></div>
        </div>
        <div class="col col-sm-6">
            <div id="toolbar"></div>
            <textarea id="editor" style="height:400px;width:100%"></textarea>
            <button onclick="ricarica()">Ricarica</button>
        </div>
    </div>
@stop
@section('extra_scripts')
    <script>
        jQuery('body').bind('set-code', function (event,code) {
            console.log('code',code,'editor',editor);
            //editAreaLoader.setValue('code_js',code.toString().match(/function[^{]+\{([\s\S]*)\}$/)[1]);
            editAreaLoader.setValue('editor',JSON.stringify(code,function(key, val) {
                if (typeof val === 'function') {
                    return val + ''; // implicitly `toString` it
                }
                return val;
            },'\t'));
            // if (editor)
            // //editor.setValue(©code,null,'\t') );
            //     editor.setValue(JSON.stringify(code,function(key, value) {
            //         if (typeof value === 'function') {
            //             return value.toString();
            //         } else {
            //             return value;
            //         }
            //     },'\t') );
        })
        function ricarica() {
            app.$set(app.$refs['menu'].lastComponent,'conf',null);
            app.$crud.waitStart();
            setTimeout( function () {
                app.$crud.waitEnd();
                var code = editAreaLoader.getValue('editor');
                console.log('code',code);
                eval('var obj = ' + code);
                console.log('obj',obj);
                app.$set(app.$refs['menu'].lastComponent,'conf',obj);
            },1000);

        };
        jQuery(function () {
            // editor = CodeMirror.fromTextArea(document.getElementById('editor'), {
            //     lineNumbers: true,
            //     mode : 'javascript'
            // });
            editAreaLoader.init({
                id: "editor"	// id of the textarea to transform
                ,start_highlight: true	// if start with highlight
                ,allow_resize: "both"
                ,allow_toggle: true
                ,word_wrap: true
                ,language: "en"
                ,syntax: "js"
            });
            //app.$set(app.$refs['menu'].lastComponent,'conf',p)

        })
    </script>
@stop
