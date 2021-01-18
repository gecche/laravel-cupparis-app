//-----------------   WIDGETS ---------------------
crud.components.widgets.wAutocomplete = Vue.component('w-autocomplete', {
    extends: crud.components.widgets.coreWAutocomplete,
    template: "#w-autocomplete-template",
    methods: {
        setRouteValues: function (route, term) {
            var that = this;
            //var r = that.$crud.createRoute(that.conf.routeName);
            route.setValues({foormName: that.foormName, viewType: that.viewType});

            //var r = new Route(routeConf);

            //var url = that.url?that.url:"/api/json/autocomplete/" + that.metadata.autocompleteModel + "?";
            var url = that.url ? that.url : route.getUrl();
            url += '?value=' + term + '&';
            route.setParams({
                field: that.name
            })

            // if (that.conf.fields) {
            //     for(var f in that.conf.fields) {
            //         url+="field[]="+that.conf.fields[f]+"&";
            //     }
            // }
            /* @TODO se metto la description diventa difficile cambiare la
             if (that.model_description) {
             for(var f in that.model_description) {
             url+="description[]="+that.model_description[f]+"&";
             }
             }
             */
            url += that.conf.separator ? '&separator=' + that.conf.separator : '';
            url += that.conf.n_items ? '&n_items=' + that.conf.n_items : '';
            url += that.conf.method ? '&method=' + that.conf.method : '';
            route.setUrl(url);
            return route;
            //return url;
        }
    }
});

crud.conf['w-b2-select2'].fieldName = null;
crud.conf['w-b2-select2'].referredDataField = null;
crud.components.widgets.wB2Select2 = Vue.component('w-b2-select2', {
    extends: crud.components.widgets.coreWB2Select2,
    template: '#w-b2-select2-template',
    methods: {
        setRouteValues: function (route) {
            var that = this;
            route.setValues({foormName: that.foormName, viewType: that.viewType});
            return route;
        },
        setReferredValue: function (data) {
            var that = this;
            if (that.value) {
                var value = that.referredDataField ?
                    that.modelData[that.referredDataField] :
                    that.referredData;
                //console.log("SETREFERREDVALUR:::", that.modelData,value);
                data.push({
                    id: that.value,
                    selected: true,
                    text: that.getLabel(value)
                });
            }
        },
        afterLoadResources: function () {
            var that = this;
            var data = [];
            //W2=this;
            // setTimeout(function () {
            //     that.afterLoadResources();
            // },2000)
            //console.log('w2-select MOUNTED',jQuery(that.$el).html());
            that.setReferredValue(data);


            that.jQe('[c-select2]').select2({
                data: data,
                ajax: that._getAjaxConf(),
                placeholder: that.translate(that.placeholder ? that.placeholder : 'app.seleziona'),
                allowClear: that.allowClear,
                theme: that.theme,
                width: "100%",
                // ajax: {
                //     url: 'https://api.github.com/search/repositories',
                //     dataType: 'json'
                //     // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                // }
            });
            that.jQe('[c-select2]').on('select2:select', function (e) {
                //console.log('value',that.getValue())
                that.change();

                //that.$emit('change',e);
            });
            that.jQe('[c-select2]').on('select2:clearing', function (e) {
                //console.log('clearing');
                that.reset();
                // if (that.defaultValue) {
                //     var newOption = new Option(that.defaultValue.text, that.defaultValue.id, false, false);
                //     jQuery(this).append(newOption);
                //     console.log('options',newOption);
                //     that.value = that.defaultValue.id;
                // } else
                //     that.value = '';


            });
            jQuery('.select2-container').addClass('form-control p-1 pl-2');
        },
        getLabel: function (value) {
            var that = this;
            var label = "";
            //console.log('getLabel value',value);
            if (value) {
                for (var i in that.labelFields) {
                    label += value[that.labelFields[i]] + " ";
                }
            }
            return label;
        },

        reset: function () {
            var that = this;
            that.value = null;
            if (that.defaultValue) {
                that.value = that.defaultValue.id;
                var newOption = new Option(that.defaultValue.text, that.defaultValue.id, false, true);
                that.jQe('[c-select2]').append(newOption).trigger('change');

                //console.log(that.jQe('[c-select2]').length,'options',newOption,that.value);
            } else
                that.value = null;

        },
        /**
         * configurazione ajax per la gestione dei risultati
         * @return {{headers: *, delay: number, method: string, data: (function(*): {field: core-w-b2-select2.methods.name, value: *}), dataType: string, processResults: (function(*): {results: []}), url: *}}
         * @private
         */
        _getAjaxConf: function () {
            var that = this;
            that.route = that._getRoute();
            that.setRouteValues(that.route);
            var url = that.route.getUrl();
            var ajax = {
                url: url,
                method: that.route.getMethod(),
                headers: Server.getHearders(),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        value: params.term,
                        field: that.fieldName ? that.fieldName : that.name,
                    }
                },
                processResults: function (json) {
                    // Tranforms the top-level key of the response object from 'items' to 'results'
                    var items = [];
                    for (var i in json.result) {
                        items.push({
                            id: json.result[i][that.primaryKey],
                            text: that.getLabel(json.result[i]),
                            record: json.result[i]
                        });
                    }
                    //console.log(that.primaryKey,'items',items);
                    return {
                        results: items
                    };
                },
            };
            return ajax;
        },
    }
});

crud.components.widgets.wB2mSelect2 = Vue.component('w-b2m-select2', {
    extends: crud.components.widgets.coreWB2mSelect2,
    template: '#w-b2m-select2-template',
});

crud.components.widgets.wBelongsto = Vue.component('w-belongsto', {
    extends: crud.components.widgets.coreWBelongsto,
    template: '#w-belongsto-template',
});

crud.components.widgets.wCheckbox = Vue.component('w-checkbox', {
    extends: crud.components.widgets.coreWCheckbox,
    template: '#w-checkbox-template',
});

crud.components.widgets.wCustom = Vue.component('w-custom', {
    extends: crud.components.widgets.coreWCustom,
    template: '#w-custom-template',
});

crud.components.widgets.wBelongstoView = Vue.component('w-belongsto-view', {
    extends: crud.components.widgets.wCustom,
    template: '#w-belongsto-view-template',
    methods: {
        getViewValue: function () {
            var that = this;
            if (that.name == 'comune') {
                return that.modelData.comune.T_COMUNE_DESC
                    + ' (' + that.modelData.provincia.T_PROVINCIA_SIGLA + ')'
                    + ' - ' + that.modelData.regione.T_REGIONE_DESC
                    + ' - ' + that.modelData.area.T_AREA_DESC;

            }
            var calculatedValue = '';
            for (var field in that.labelFields) {
                calculatedValue += that.value[that.labelFields[field]] + ' - ';
            }
            console.log("CALCULATED::: ", calculatedValue);
            return calculatedValue.substring(0, (calculatedValue.length - 3));
        }
    }
});

crud.components.widgets.wDatePicker = Vue.component('w-date-picker', {
    extends: crud.components.widgets.coreWDatePicker,
    template: '#w-date-picker-template',
});

crud.components.widgets.wDateSelect = Vue.component('w-date-select', {
    extends: crud.components.widgets.coreWDateSelect,
    template: '#w-date-select-template',
});

crud.components.widgets.wDownload = Vue.component('w-download', {
    extends: crud.components.widgets.coreWDownload,
    template: '#w-download-template',
});

crud.conf['w-hasmany'].bgClass = 'bg-warning-soft';
crud.components.widgets.wHasmany = Vue.component('w-hasmany', {
    extends: crud.components.widgets.coreWHasmany,
    template: '#w-hasmany-template',
    // data : function () {
    //     var _conf = this._getConf();
    //     return {
    //         bgClass : _conf.bgClass || 'bg-warning-soft',
    //     }
    // }
});

crud.conf['w-hasmany-listed'] = {
    lastRefId: 0,
    confParent: 'crud.conf.w-hasmany',
    hasManyListConf: {
        routeName: null,
        actions: ['delete', 'newItem'],

        methods: {

        },
        mounted: function () {

        },
        customActions: {
            delete: {
                type: 'record',
                icon: 'fa fa-trash text-danger',
                css: 'btn-outline-danger',
                execute: function () {
                    var that = this;
                    // console.log("MODELDATA::: ", that.modelData);
                    // console.log("MDATA::",that.modelData);
                    // jQuery(that.$el).closest('tr').remove();
                    console.log("B::: ", that.view.value);
                    for (var i = 0; i < that.view.value.length; i++) {
                        console.log("I::: ", i, that.modelData.refId);
                        if (that.modelData.refId === that.view.value[i].refId) {
                            that.view.value.splice(i, 1);
                            that.view.loading = true;
                            setTimeout(function () {
                                that.view.reload();
                            }, 50)
                            return;
                        }
                    }
                }
            },
            newItem: {
                type: 'collection',
                icon: 'fa fa-plus text-success',
                css: 'btn-outline-success',
                execute: function () {
                    var that = this;

                    if (that.view.value.length >= that.view.limit) {
                        that.messageDialog('Limite massimo raggiunto');
                        return;
                    }
                    var values = that.merge({}, that.view.fieldsDefaults);
                    values.refId = 'a' + Math.random();
                    that.view.value.push(values);
                    that.view.reload();
                }
            }
        }
    }
}
crud.components.widgets.wHasmanyListed = Vue.component('w-hasmany-listed', {
    extends: crud.components.widgets.wHasmany,
    template: '#w-hasmany-listed-template',

    methods: {
        dynamicData: function (conf) {
            var that = this;
            conf.hasManyListConf.cRef = that._uid + conf.hasManyListConf.hasManyName;
            conf.hasManyListConf.value = conf.value;
            for (var i in conf.hasManyListConf.value) {
                conf.hasManyListConf.value[i].refId = i;
                conf.hasManyListConf.value[i].status = 'updated';
            }
            conf.hasManyListConf.fieldsConfig = that.merge(conf.hasManyListConf.fieldsConfig, conf.relationConf);
            console.log("hasManyLISTCONF::: ", conf, that);
            conf.hasManyListConf.methods.getFieldName = function (key) {

                return conf.hasManyListConf.hasManyName + "-" + key + '[]';

            }
            return conf;
        }
    }
});


crud.conf['w-hasmany-listed-constraint'] = {
    lastRefId: 0,
    confParent: 'crud.conf.w-hasmany',
    hasManyListConf: {
        routeName: 'list-constraint',
        actions: ['action-delete', 'action-insert-popup', 'action-edit-popup'],

        methods: {

        },
        mounted: function () {

        },
        customActions: {
            // 'action-edit' :
        }
    }
}
crud.components.widgets.wHasmanyListedConstraint = Vue.component('w-hasmany-listed-constraint', {
    extends: crud.components.widgets.wHasmany,
    template: '#w-hasmany-listed-constraint-template',

    methods: {
        dynamicData: function (conf) {
            var that = this;
            conf.hasManyListConf.cRef = that._uid + conf.hasManyListConf.hasManyName;
            conf.hasManyListConf.fieldsConfig = that.merge(conf.hasManyListConf.fieldsConfig, conf.relationConf);
            console.log("hasManyLISTCONF::: ", conf, that);
            conf.hasManyListConf.constraintValue = conf.modelData.id;
            conf.hasManyListConf.methods.getFieldName = function (key) {

                return conf.hasManyListConf.hasManyName + "-" + key + '[]';

            }
            return conf;
        }
    }
    // data : function () {
    //     var _conf = this._getConf();
    //     return {
    //         bgClass : _conf.bgClass || 'bg-warning-soft',
    //     }
    // }
});

crud.conf['w-hasmany-listed-squared-constraint'] = {
    lastRefId: 0,
    confParent: 'crud.conf.w-hasmany',
    hasManyListConf: {
        routeName: 'list-constraint',
        actions: ['action-delete', 'action-insert-popup', 'action-edit-popup'],

        methods: {

        },
        mounted: function () {

        },
        customActions: {
            // 'action-edit' :
        }
    }
}
crud.components.widgets.wHasmanyListedSquaredConstraint = Vue.component('w-hasmany-listed-squared-constraint', {
    extends: crud.components.widgets.wHasmanyListedConstraint,
    template: '#w-hasmany-listed-squared-constraint-template',

    methods: {
        dynamicData: function (conf) {
            var that = this;
            conf.hasManyListConf.cRef = that._uid + conf.hasManyListConf.hasManyName;
            conf.hasManyListConf.fieldsConfig = that.merge(conf.hasManyListConf.fieldsConfig, conf.relationConf);
            console.log("hasManyLISTCONF::: ", conf, that);
            conf.hasManyListConf.constraintValue = conf.modelData.id;
            conf.hasManyListConf.methods.getFieldName = function (key) {

                return conf.hasManyListConf.hasManyName + "-" + key + '[]';

            }
            return conf;
        }
    }
});


crud.components.widgets.wHasmanyThrough = Vue.component('w-hasmany-through', {
    extends: crud.components.widgets.coreWHasmanyThrough,
    template: '#w-hasmany-through-template',
});

crud.components.widgets.wHasmanyList = Vue.component('w-hasmany-list', {
    extends: crud.components.widgets.coreWHasmanyList,
    template: '#w-hasmany-list-template',
});

crud.conf['w-hasmany-view'].titleClass = 'text-amber-900';
crud.components.widgets.wHasmanyView = Vue.component('w-hasmany-view', {
    extends: crud.components.widgets.coreWHasmanyView,
    template: '#w-hasmany-view-template',
    // data : function () {
    //     var _conf = this._getConf();
    //     return {
    //         titleClass : _conf.titleClass || ' text-amber-900',
    //     }
    // }
});

crud.conf['w-hasone'] = {
    confParent: 'crud.conf.w-hasmany',
    nullable: false,
    limit: 1
}

crud.components.widgets.wHasone = Vue.component('w-hasone', {
    extends: crud.components.widgets.coreWHasmany,
    template: '#w-hasone-template',
});


crud.conf['w-hasone-real'] = {
    confParent: 'crud.conf.w-base',
    nullable: false,
    bgClass: 'bg-warning-soft',
    viewConf: {
        routeName: null,
        actions: [],
        value: {},
    },
    loaded: false,
}

crud.components.widgets.wHasoneReal = Vue.component('w-hasone-real', {
    extends: crud.components.widgets.wBase,
    template: '#w-hasone-real-template',
    mounted: function () {
        var that = this;
        //that.viewConf.fields = ['status'];
        that.viewConf.value = that.value || {};
        that.viewConf.cRef = that._uid + "_vhasone";
        that.loaded = true;
        that.$forceUpdate();
        console.log('viewConf', that.viewConf);
    },
});


crud.conf['w-hasone-view'] = {
    confParent: 'crud.conf.w-hasone',
    titleClass: ' text-amber-900',
}

crud.components.widgets.wHasoneView = Vue.component('w-hasone-view', {
    extends: crud.components.widgets.wHasone,
    template: '#w-hasone-view-template',
});

crud.components.widgets.wHidden = Vue.component('w-hidden', {
    extends: crud.components.widgets.coreWHidden,
    template: '#w-hidden-template'
});

crud.components.widgets.wImage = Vue.component('w-image', {
    extends: crud.components.widgets.coreWImage,
    template: '#w-image-template'
});

crud.conf['w-input'].labelGroup = true;
crud.components.widgets.wInput = Vue.component('w-input', {
    extends: crud.components.widgets.coreWInput,
    template: '#w-input-template',
    // data : function () {
    //     var _conf = this._getConf();
    //     return {
    //         label : _conf.label?_conf.label:'',
    //     }
    // }
});

crud.conf['w-input-view'] = {
    confParent: 'crud.conf.w-input',
}

crud.components.widgets.wInputView = Vue.component('w-input-view', {
    extends: crud.components.widgets.wInput,
    template: '#w-input-view-template',
});

crud.components.widgets.wInputHelped = Vue.component('w-input-helped', {
    extends: crud.components.widgets.coreWInputHelped,
    template: '#w-input-helped-template',
});

crud.components.widgets.wPreview = Vue.component('w-preview', {
    extends: crud.components.widgets.coreWPreview,
    template: '#w-preview-template',
});

crud.components.widgets.wRadio = Vue.component('w-radio', {
    extends: crud.components.widgets.coreWRadio,
    template: '#w-radio-template',
});

crud.components.widgets.wSelect = Vue.component('w-select', {
    extends: crud.components.widgets.coreWSelect,
    template: '#w-select-template',
});

crud.conf['w-swap'].switchClass = 'form-switch-success';
crud.conf['w-swap'].dataSwitched = false;

crud.components.widgets.wSwap = Vue.component('w-swap', {
    extends: crud.components.widgets.coreWSwap,
    template: '#w-swap-template',
    // data : function() {
    //     var _c = this._getConf();
    //     return {
    //         switchClass : _c.switchClass || 'form-switch-success',
    //         dataSwitched : _c.dataSwitched || false,
    //     }
    // },
    methods: {
        setRouteValues: function (route) {
            var that = this;
            console.log('rswap RIDEFINITO', that.modelName, that.modelName)
            var dV = that.getDV();
            var keys = Object.keys(dV);
            var value = that.value ? that.value : keys[0];
            var vs = keys.map(String);
            var index = vs.indexOf("" + value);
            index = (index + 1) % vs.length;
            console.log('rswap', that);
            route.setValues({
                modelName: that.modelName,
                //field : that.name, //that.conf.key?that.conf.key:that.cKey,
                //value : keys[index]
            });
            route.setParams({
                id: that.modelData.id,
                field: that.name,
                value: keys[index]
            });
            return route;
        }
    }

});


crud.conf['w-swap-smarty'] = {
    confParent: 'crud.conf.w-swap',
    switchClass: 'form-switch-success',
    dataSwitched: false,
}
crud.components.widgets.wSwapSmarty = Vue.component('w-swap-smarty', {
    extends: crud.components.widgets.wSwap,
    template: '#w-swap-smarty-template',
    // data : function() {
    //     var _c = this._getConf();
    //     return {
    //         switchClass : _c.switchClass || 'form-switch-success',
    //         dataSwitched : _c.dataSwitched || false,
    //     }
    // },
    methods: {
        _swap: function (key) {
            var that = this;
            var r = that._getRoute();
            that.setRouteValues(r);
            var dV = that.getDV();
            Server.route(r, function (json) {
                if (json.error) {
                    that.errorDialog(json.msg);
                    return;
                }
                //that.value = key;
                that.slot = dV[key];
                that.change(json);
            })
        }
    }
});


crud.components.widgets.wStatus = Vue.component('w-status', {
    extends: crud.components.widgets.coreWStatus,
    template: '#w-status-template',
});

crud.components.widgets.wText = Vue.component('w-text', {
    extends: crud.components.widgets.coreWText,
    template: '#w-text-template'
});

crud.conf['w-textarea'].maxlength = null;
crud.conf['w-textarea'].charleft = false;
crud.components.widgets.wTextarea = Vue.component('w-textarea', {
    extends: crud.components.widgets.coreWTextarea,
    template: '#w-textarea-template',
    // data : function () {
    //     var _c = this._getConf();
    //     return {
    //         maxlength : _c.maxlength || null,
    //         charleft : _c.charleft || false,
    //     }
    // }
});

crud.components.widgets.wTextareaView = Vue.component('w-textarea-view', {
    extends: crud.components.widgets.wTextarea,
    template: '#w-textarea-view-template',
});


crud.components.widgets.wTexthtml = Vue.component('w-texthtml', {
    extends: crud.components.widgets.coreWTexthtml,
    template: '#w-texthtml-template',
});

crud.components.widgets.wUpload = Vue.component('w-upload', {
    extends: crud.components.widgets.coreWUpload,
    template: '#w-upload-template',
});

crud.components.widgets.wUploadAjax = Vue.component('w-upload-ajax', {
    extends: crud.components.widgets.coreWUploadAjax,
    template: '#w-upload-ajax-template',
});

crud.components.widgets.wMap = Vue.component('w-map', {
    extends: crud.components.widgets.coreWMap,
    template: '#w-map-template',
    methods: {
        dynamicData: function (conf) {
            var that = this;
            var _md = conf.modelData || {};
            if (_md[conf.latName]) {
                conf.lat = parseFloat(_md[conf.latName]);
            }
            if (_md[conf.lngName]) {
                conf.lng = parseFloat(_md[conf.lngName]); //[conf.lngName];
            }
            //conf.lat = parseFloat(conf.modelData[conf.latName]); //[conf.latName];
            //conf.lng = parseFloat(conf.modelData[conf.lngName]); //[conf.lngName];
            conf.apiKey = this.$crud.apiKey;
            return conf;
        }
    }
});

crud.components.widgets.wMapView = Vue.component('w-map-view', {
    extends: crud.components.widgets.coreWMapView,
    template: '#w-map-view-template',
    methods: {
        dynamicData: function (conf) {
            var that = this;
            var _md = conf.modelData || {};
            if (_md[conf.latName]) {
                conf.lat = parseFloat(_md[conf.latName]);
            }
            if (_md[conf.lngName]) {
                conf.lng = parseFloat(_md[conf.lngName]); //[conf.lngName];
            }
            //conf.lat = parseFloat(conf.modelData[conf.latName]); //[conf.latName];
            //conf.lng = parseFloat(conf.modelData[conf.lngName]); //[conf.lngName];
            conf.apiKey = this.$crud.apiKey;
            return conf;
        }
    }
});

crud.components.widgets.wDateText = Vue.component('w-date-text', {
    extends: crud.components.widgets.coreWDateText,
    template: '#w-date-text-template',
});

//-----------------   VIEWS  ----------------------
crud.conf['v-edit'].beforeForm = null;

crud.components.views.vEdit = Vue.component('v-edit', {
    extends: crud.components.views.coreVEdit,
    template: '#v-edit-template',
    methods: {
        dynamicData: function (conf) {
            if (!conf.langContext && conf.langContext !== null) {
                conf.langContext = conf.modelName ? conf.modelName : this.cModel
                conf.langContext += '.fields';
            }
            return conf;
        },
    }
});

crud.components.views.vHasone = Vue.component('v-hasone', {
    extends: crud.components.views.coreVHasone,
    template: '#v-hasone-template',
});

crud.components.views.vHasmany = Vue.component('v-hasmany', {
    extends: crud.components.views.coreVHasmany,
    template: '#v-hasmany-template',
});

crud.conf['v-hasmany-listed'] = {
    confParent: 'crud.conf.v-hasmany',
    item: {}
}
crud.components.views.vHasmanyListed = Vue.component('v-hasmany-listed', {
    extends: crud.components.views.vHasmany,
    template: '#v-hasmany-listed-template',
});

crud.components.views.vHasmanyView = Vue.component('v-hasmany-view', {
    extends: crud.components.views.coreVHasmanyView,
    template: '#v-hasmany-view-template',
});

crud.components.views.vHasmanyList = Vue.component('v-hasmany-list', {
    extends: crud.components.views.coreVHasmanyList,
    template: '#v-hasmany-list-template',
});

crud.components.views.vInsert = Vue.component('v-insert', {
    extends: crud.components.views.coreVInsert,
    template: '#v-insert-template',
    methods: {
        dynamicData: function (conf) {
            if (!conf.langContext && conf.langContext !== null) {
                conf.langContext = conf.modelName ? conf.modelName : this.cModel
                conf.langContext += '.fields';
            }
            return conf;
        },
    }
});

crud.conf['v-list'].helpText = '';
crud.conf['v-list'].hasFooter = true;
crud.components.views.vList = Vue.component('v-list', {
    extends: crud.components.views.coreVList,
    template: '#v-list-template',
    methods: {
        dynamicData: function (conf) {
            if (!conf.langContext && conf.langContext !== null) {
                conf.langContext = conf.modelName ? conf.modelName : this.cModel
                conf.langContext += '.fields';
            }
            return conf;
        },
        hasHelp: function (key) {
            var that = this;
            if (this.fieldsConfig[key]) {
                return this.fieldsConfig[key].helpText || false;
            }
        }
    }
})

crud.components.views.vListEdit = Vue.component('v-list-edit', {
    extends: crud.components.views.coreVListEdit,
    template: '#v-list-edit-template',
    methods: {
        dynamicData: function (conf) {
            if (!conf.langContext && conf.langContext !== null) {
                conf.langContext = conf.modelName ? conf.modelName : this.cModel
                conf.langContext += '.fields';
            }
            return conf;
        },
    }
});

crud.components.views.vSearch = Vue.component('v-search', {
    extends: crud.components.views.coreVSearch,
    template: '#v-search-template',
    methods: {
        dynamicData: function (conf) {
            if (!conf.langContext && conf.langContext !== null) {
                conf.langContext = conf.modelName ? conf.modelName : this.cModel
                conf.langContext += '.fields';
            }
            return conf;
        },
    }
});
crud.conf['v-view'].defaultWidgetType = 'w-input-view';
crud.components.views.vView = Vue.component('v-view', {
    extends: crud.components.views.coreVView,
    template: '#v-view-template',
    methods: {
        dynamicData: function (conf) {
            if (!conf.langContext && conf.langContext !== null) {
                conf.langContext = conf.modelName ? conf.modelName : this.cModel
                conf.langContext += '.fields';
            }
            return conf;
        },
    }
});
//-----------------   ACTIONS ---------------------

crud.components.actions.actionBase = Vue.component('action-base', {
    extends: crud.components.actions.coreActionBase,
    template: '#action-template'
});

Vue.component('action-edit', {
    extends: crud.components.actions.actionBase
});

Vue.component('action-view', {
    extends: crud.components.actions.actionBase
});

crud.components.actions.actionSave = Vue.component('action-save', {
    extends: crud.components.actions.actionBase,
    template: '#action-square-template'
});

Vue.component('action-save-back', {
    extends: crud.components.actions.actionSave,
    template: '#action-square-template'
});

Vue.component('action-insert', {
    extends: crud.components.actions.actionBase
});

Vue.component('action-back', {
    extends: crud.components.actions.actionBase,
    template: '#action-square-template'
});

Vue.component('action-search', {
    extends: crud.components.actions.actionBase,
    template: '#action-square-template'
});

Vue.component('action-reset', {
    extends: crud.components.actions.actionBase,
    template: '#action-square-template'
});

Vue.component('action-delete', {
    extends: crud.components.actions.actionBase
});

Vue.component('action-delete-selected', {
    extends: crud.components.actions.actionBase
});

Vue.component('action-edit-mode', {
    extends: crud.components.actions.actionBase
});

Vue.component('action-view-mode', {
    extends: crud.components.actions.actionBase
});

Vue.component('action-save-row', {
    extends: crud.components.actions.actionBase
});


crud.components.actions.actionOrder = Vue.component('action-order', {
    extends: crud.components.actions.coreActionOrder,
    template: '#action-order-template'
});


//-----------------   MISCELLANEOUS ---------------------

crud.components.misc.cLoading = Vue.component('c-loading', {
    extends: crud.components.misc.coreCLoading,
    template: '#c-loading-template',
});

crud.components.misc.cPaginator = Vue.component('c-paginator', {
    extends: crud.components.misc.coreCPaginator,
    template: '#c-paginator-template',
});

crud.components.misc.cWait = Vue.component('c-wait', {
    extends: crud.components.misc.coreCWait,
    template: '#c-wait-template',
});


Vue.component('tpl-record', {
    extends: crud.components.misc.tplBase,
    template: '#tpl-record-template'
});

Vue.component('tpl-record2', {
    extends: crud.components.misc.tplBase,
    template: '#tpl-record2-template'
});

Vue.component('tpl-list', {
    extends: crud.components.misc.tplBase,
    template: '#tpl-list-template'
});

Vue.component('tpl-no', {
    extends: crud.components.misc.tplBase,
    template: '#tpl-no-template'
});

Vue.component('tpl-full-no', {
    extends: crud.components.misc.tplBase,
    template: '#tpl-full-no-template'
});

crud.components.misc.dConfirm = Vue.component('d-confirm', {
    extends: crud.components.misc.coreDConfirm,
    template: '#d-confirm-template'
});

crud.components.misc.dMessage = Vue.component('d-message', {
    extends: crud.components.misc.coreDMessage,
    template: '#d-message-template'
});

crud.components.misc.dError = Vue.component('d-error', {
    extends: crud.components.misc.coreDError,
    template: '#d-error-template'
});
crud.components.misc.dWarning = Vue.component('d-warning', {
    extends: crud.components.misc.coreDWarning,
    template: '#d-warning-template'
});

crud.components.misc.dCustom = Vue.component('d-custom', {
    extends: crud.components.misc.coreDCustom,
    template: '#d-custom-template'
});
