@extends('app')
{{--@section('content')--}}
{{--    <div id="app">--}}
{{--        <div id="compo">--}}

{{--        </div>--}}
{{--        <div id="compo1">--}}

{{--        </div>--}}
{{--        <div id="compo2">--}}

{{--        </div>--}}
{{--    </div>--}}

{{--@stop--}}
{{--@section('extra_scripts')--}}
{{--    <script>--}}
{{--        var c = Vue.component('c',{--}}
{{--            data : function () {--}}

{{--                return {--}}
{{--                    varC : 'Variabile'--}}
{{--                }--}}
{{--            },--}}
{{--            template : '<div> @{{ varC }} originale </div>'--}}
{{--        });--}}
{{--        var c1 = Vue.component('c',{--}}
{{--            extends : c,--}}
{{--            data : function () {--}}

{{--                return {--}}
{{--                    varC : 'Varmod',--}}
{{--                    varC1 : 'Variabile1'--}}
{{--                }--}}

{{--            },--}}
{{--            template : '<div> @{{ varC }} e @{{varC1}}</div>'--}}
{{--        });--}}
{{--        var c2 = Vue.component('c2',{--}}
{{--            extends : c1,--}}
{{--        });--}}

{{--        var compo = new c();--}}
{{--        compo.$mount('#compo');--}}

{{--        var compo1 = new c1();--}}
{{--        compo1.$mount('#compo1');--}}

{{--        var compo2 = new c2();--}}
{{--        compo2.$mount('#compo2');--}}
{{--    </script>--}}
{{--@stop--}}



@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css" rel="stylesheet">

    <script>
        var c = Vue.component('c',{
            mounted : function() {
                console.log('mounted c');
                jQuery(function () {
                    var r = new Route({
                        url : 'ciccio/{param1?}/{param2?}/{param3?}'
                    });
                    r.setValues({
                        //param1 : 'p1',
                        param2 : 'p2'
                    });
                    console.log('URL',r.getUrl())
                })
            },
            data : function () {
                console.log('DATa c');
                return {
                    varC : 'Variabile'
                }
            },
            methods : {
                func1 : function () {
                    console.log('func1 originale')
                }
            }
        });
        var c1 = Vue.component('c1',{
            extends : c,
            mounted : function() {
                console.log('mounted c1');
            },
            _privateFunc : function() {
                return 'privato';
            },
            data : function () {
                console.log('DATA c1')
                return {
                    varC : 'Varmod',
                    varC1 : 'Variabile1'
                }

            },
            methods : {
                func1 : function () {
                    console.log('func1 estesa ' + this._privateFunc())
                }
            }
        });
        var c2 = Vue.component('c2',{
            extends : c1,
            mounted : function() {
                console.log('mounted c2');
            },
            data : function () {
                console.log('DATA c2');
                return {
                    varc2 : 'var c2'
                }
            },
            template : '<div> @{{ varC }} originale <button v-on:click="func1()">ok</button></div>'
        })
        myConf = {
            name : 'rpvoa',

            modelName : 'user',
            type : 'w-swap',
            modelData : {
                id : 1
            }
        }
        myListConf = {
            modelName : 'user',
            fields : ['email','field1','banned'],
            fieldsConfig : {
                banned : {
                    type : 'w-swap2',
                    modelName : 'user'
                },
                email : {
                    type : 'w-text',
                    // mounted : function () {
                    //     console.log('w-text email mounted');
                    // }
                },
                field1 : {
                    type : 'w-custom',
                    mounted : function () {
                        console.log('w-custom field1 mounted');
                        this.value = 'field1 ' + this.modelData.email;
                    }
                }
            }
        }

        myRecordConf = {
            modelName : 'user',
            pk : 3,
        }
        var selConf = {
            value : 48,
            cRef : 'pippo',
            modelName : 'anagrafica',
            name : 'nazione_id',
            fields : ['descrizione'],
            methods : {
                change : function () {
                    alert(this.getValue())
                }
            },
            modelData : {
                id:48,
                descrizione : 'Lituania'
            },
            allowClear : true,
        }
        var autoConf = {
            value : 48,
            modelName : 'anagrafica',
            name : 'nazione_id',
            fields : ['descrizione'],
            modelData : {
                id:48,
                descrizione : 'Lituania'
            },
            methods : {
                change : function () {
                    console.log('autocomplete value',this.getValue());
                }
            }
        }
        // fields per la viewlist con colonne dinamiche
        UNSETFIELDS = 0;
        var dinamicColumn = {
            unsetFields : 0,
            methods : {
                setRouteValues : function (route) {
                    route.setUrl('/test-json-list/');
                    route.setParams({'unsetFields':UNSETFIELDS});
                    return route;
                }
            }
        }

        var cManageColumn = {
            modelName : 'comune',
            listConf : dinamicColumn,
        }

        var comunedefaultWidgetType = {
            modelName : 'comune',
            defaultWidgetType : 'w-text'
        }
        window.addEventListener('crud-app-loaded', function(event) {
            //the event occurred
            //alert('aaa');
            var a = new crud.components.actions.actionBase({
                data : function () {
                    return {
                        text : 'ciao',
                        css : 'btn btn-outline-primary btn-small',
                        execute : function () {
                            alert('bbb')
                        }
                    }
                }
            });
            a.$mount('[data-action-id]');
        })

        var comuneConf = {
            modelName : 'user',
            cRef : 'v-comune',
        }
        // crud.EventBus.on('crud-app-loaded',function () {
        //     alert('aaa');
        // })





        wizardConf = {
            modelName : 'user',
            title : 'Titolo Wizard',
            actions : ['action-previous','action-next'],
            fieldsStep : {
                0 : ['field1','field2','field3'],
                1 : [],
                2 : []
            },
            fieldsStepConfig : {
                0 : {},
                1 : {
                    'field4' : {
                        type : 'w-text'
                    }
                },
                2 : {}
            },
            routeName : 'wizard',
            maxStep : 3,
            methods : {
                wizardInit : function () {
                    var that = this;
                    that.title = 'Passo 0';
                    // that.routeIn = new Route({
                    //     url : '/test-passo/{passo}',
                    //     values : {
                    //         passo : 0
                    //     },
                    //     method : 'get',
                    //     type : 'record'
                    // });
                    // that.routeOut = new Route({
                    //     url : '/test-passo/{passo}',
                    //     values : {
                    //         passo : 0
                    //     },
                    //     method : 'post',
                    //     type : 'record'
                    // });
                    //
                    // that.fetchData(that.routeIn,function (json) {
                    //     that.fillData(that.routeIn,json);
                    //     that.draw();
                    // });
                },
                backward : function () {
                    var that = this;
                    that.title = 'Passo ' + this.currentStep;
                    that.loading = true;
                    that.setRouteValues(that.route);
                    that.fields = that.fieldsStep[that.currentStep];
                    that.fieldsConfig = that.fieldsStepConfig[that.currentStep];
                    that.fetchData(that.route,function (json) {
                        that.fillData(that.route,json);
                        that.draw();
                    });
                },
                forward : function () {
                    var that = this;
                    that.title = 'Passo ' + this.currentStep;
                    that.loading = true;
                    that.setRouteValues(that.route);
                    that.fields = that.fieldsStep[that.currentStep];
                    that.fieldsConfig = that.fieldsStepConfig[that.currentStep];
                    that.fetchData(that.route,function (json) {
                        that.fillData(that.route,json);
                        that.draw();
                    });

                },

                validate : function (action,callback) {
                    console.log('action',action,callback);
                    return callback(true);
                }

            }
        };

        dragConf = {
            routeName : null,
            actions : ['action-save-all'],
            title : 'Titolo',
            customActions : {
                'action-save-all': {
                    text : 'Salva',
                    type : 'collection',
                    execute : function () {
                        var that = this;
                        console.log(this.view.getBuckets())
                    }
                }
            },
            value : [
                {
                    id : 1,
                    label : 'Operatore1',
                    items : [
                        {
                            id : 1,
                            label : 'provincia1'
                        },
                        {
                            id : 2,
                            label : 'provincia2'
                        },
                        {
                            id : 3,
                            label : 'provincia3'
                        }
                    ]
                },
                {
                    id : 2,
                    label : 'Operatore2',
                    items : [
                        {
                            id : 4,
                            label : 'provincia4'
                        },
                        {
                            id : 5,
                            label : 'provincia5'
                        },
                        {
                            id : 6,
                            label : 'provincia6'
                        }
                    ]
                },
                {
                    id : 3,
                    label : 'Operatore3',
                    items : [
                    ]
                }
            ]
        }

    </script>

    <div id="app">
        <div>
            <a class="m-3" href="#v-list?cModel=comune" title="lista calcolata con il solo nome modello">Comuni List </a>
            <a class="m-3" href="#v-search?cModel=comune" title="search calcolata con il solo nome modello">Comuni Search </a>
            <a class="m-3" href="#v-edit?cModel=comune&cPk=1" title="edit calcolata con il solo nome modello e pk">Edit Comune</a>

            <a class="m-3" href="#v-search?cModel=provincia">Province Search</a>
            <a class="m-3" href="#c-manage?cModel=comune">Comuni Manage </a>
            <a class="m-3" href="#c-manage?cModel=provincia">Province Manage </a>
        </div>
        <div>
            <a class="m-3" href="#v-list-edit?cModel=nazione">Nazione ListEdit </a>
            <a class="m-3" href="#v-list-edit?cModel=provincia">Province ListEdit </a>
            <a class="m-3" href="#v-search?cModel=comune">Comuni Search </a>
            <a class="m-3" href="#v-search?cModel=provincia">Province Search</a>
            <a class="m-3" href="#c-manage?cModel=nazione&cInlineEdit=1">Nazioni Manage ListEdit </a>
            <a class="m-3" href="#c-manage?cModel=costante_fatturazione&cInlineEdit=1">Costante fatturazione Manage ListEdit</a>
        </div>
        <div>
            <a class="m-3" href="#v-edit?cConf=comuneConf&cPk=1">Comuni Edit confcustom </a>
            <a class="m-3" href="#v-edit?cConf=myRecordConf">User Edit con pk in conf </a>
            <a class="m-3" href="#v-list?cConf=dinamicColumn">Lista con colonne dinamiche </a>
            <a class="m-3" href="#c-manage?cConf=cManageColumn">Manage con lista con colonne dinamiche</a>
{{--            <a class="m-3" href="#c-manage?cModel=comune">Comuni Manage </a>--}}
{{--            <a class="m-3" href="#c-manage?cModel=provincia">Province Manage </a>--}}
        </div>
        <div>
            <a class="m-3" href="#v-edit?cConf=comunedefaultWidgetType&cPk=1">Comuni Edit con defaultWidgetType diverso da default  </a>
            <a class="m-3" href="#c-wizard?cConf=wizardConf">Wizard</a>
            <a class="m-3" href="#c-drag-drop?cConf=dragConf">Lista con colonne dinamiche </a>
{{--            <a class="m-3" href="#c-manage?cConf=cManageColumn">Manage con lista con colonne dinamiche</a>--}}
        </div>
        <c-router ref="menu" inline-template c-content-id="#component-area">
            <div id="component-area"></div>
        </c-router>

        <div data-action-id="myAction"></div>
{{--        <w-b2-select2 c-conf="selConf"></w-b2-select2>--}}

        <c-wizard c-conf="wizardConf"></c-wizard>
        {{--<c-drag-drop c-conf="dragConf"></c-drag-drop>--}}

{{--        <w-autocomplete2 c-conf="autoConf"></w-autocomplete2>--}}

{{--        <c2></c2>--}}
{{--        <c></c>--}}
{{--        <div>istanza diretta</div>--}}
{{--        <div>--}}
{{--            <w-swap c-conf="myConf"></w-swap>--}}
{{--        </div>--}}
{{--        <div>istanza tramite template</div>--}}
{{--        <div>--}}
{{--            <tpl-list c-render="myConf"></tpl-list>--}}
{{--        </div>--}}
{{--        <div>istanza tramite v-render</div>--}}
{{--        <div>--}}

{{--            <v-list c-model="user"></v-list>--}}

{{--                    <v-list c-conf="myListConf"></v-list>--}}

{{--            <v-edit c-model="user" c-pk="3"></v-edit>--}}
{{--            <v-search c-model="user"></v-search>--}}
{{--            <v-edit c-model="user" c-pk="1"></v-edit>--}}
{{--            <v-insert c-model="anagrafica"></v-insert>--}}
{{--            <v-view c-model="user" c-pk="2"></v-view>--}}
{{--        <v-search c-model="user"></v-search>--}}
{{--        <div class="row">--}}
{{--            <div class="col-6">--}}
{{--                <c-manage c-model="user"></c-manage>--}}
{{--            </div>--}}
{{--            <div class="col-6">--}}
{{--                <c-manage c-model="user"></c-manage>--}}
{{--            </div>--}}
{{--        </div>--}}


    </div>

@stop
@section('extra_scripts')

@stop
