crud.conf['csv-dashboard'] = {
    jobId : null,
    progressValue : 20,
    saveEnabled : false,
    uploadEnabled : true,
    progressEnabled : false,
    status : 'upload',
    confUpload : {
        name : 'resource',
        type : 'w-upload-ajax',
        maxFileSize: "2M",
        template : 'tpl-no',
        modelName : null, //'cup_geo_nazioni_istat',
        extensions: [
            "csv",
        ],
        ajaxFields: {
            field : 'resource',
        },
        methods :  {
            change() {
                // TODO gestire eventuale errore
                var that = this;
                var viewUpload = that.getComponent('viewUpload');
                viewUpload.getAction('action-save').setEnabled(true);
            }
        }
    },
    viewUpload : {
        cRef : 'viewUpload',
        routeName : 'datafile_insert',
        fields : [],
        actions : ['action-save','action-cancel'],
        customActions : {
            'action-save': {
                icon : null,
                text : 'app.importa-csv'
            }
        }
    },
    viewSave : {
        methods : {
            setRouteValues : function (route) {
                if (route) {
                    route.setValues({
                        jobId : this.$parent.jobId,
                        modelName : this.$parent.providerName,
                    });
                }
                return route;
            }
        },
        cRef : 'viewSave',
        routeName : 'datafile_import',
        fields :[],
        actions : ['action-save-import'],
        customActions : {
            'action-save-import': {
                text : 'Salva Csv Caricato',
                css : 'btn bnt-outline-secondary btn-info',
                type : 'record',
            }
        }
    },
    viewList : {
        cRef : 'csvViewList',
        errorClass : 'bg-danger-soft .border-top .border-primary .bw--2',
        routeName : 'datafile_data',
        actions : ['action-mostra-tutti','action-show-error'],
        showError : false,
        canEdit : false,
        multiSheets : false,
        // configurazione select
        selectSheetConf : {
            cRef : 'sheetSelect',
            methods : {
                change() {
                    var sheetName = this.domainValues[this.getValue()];
                    this.$parent.setSheet(sheetName);
                }
            }
        },
        editError : {
            title : 'app.modifica',
            icon : 'fa fa-edit',
            execute() {
                alert('modirica' + this.index + " " + this.key);
            }
        },
        customActions : {
            'action-show-error' : {
                text : 'Mostra solo errori',
                css : 'btn-outline-danger',
                type : 'collection',
                execute() {
                    this.view.showError = true;
                    this.view.reload();
                },
                visible() {
                    if (this.view.metadata.has_datafile_errors)
                        return true
                    return false;
                }
            },
            'action-mostra-tutti' : {
                text : 'Mostra tutti',
                type : 'collection',
                execute() {
                    this.view.showError = false;
                    this.view.reload();
                },
                visible() {
                    if (this.view.metadata.has_datafile_errors)
                        return true
                    return false;
                }

            }
        },
        methods : {

            editErrorConf(index,key) {
                var that = this;
                var conf =  that.merge(that.editError,{
                    index : index,
                    key : key,
                    view : that,
                })
                console.log('conf Edit',conf);
                return conf;
            },
            setErrors() {
                var that = this;
                console.log('csvdata ',this);
                for (var i in that.value) {
                    var rowData = that.value[i];
                    if (rowData.errors && rowData.errors.length) {
                        var rowJQ = jQuery(that.jQe().find('tbody tr').get(i));
                        for (var c in rowData.errors) {
                            var error = rowData.errors[c];
                            console.log("found error",'.field-'+error.field_name,error.field_name,rowJQ.find('.field-'+error.field_name).length);
                            var colJQ = rowJQ.find('.field-'+error.field_name);
                            colJQ.addClass('danger')
                                .attr('data-toggle',"tooltip")
                                .attr('data-errors',error)
                                .attr('title',error.error_name);
                            //.attr('title','bosadf afdaf ');
                            $(colJQ).tooltip({
                                container : 'body',
                                html : true,
                            });
                            if (that.hasClassError(error.field_name)) {
                                colJQ.attr('data-error_index',i+":"+c);
                                colJQ.click(function () {
                                    var error_index = $(this).attr('data-error_index');
                                    var idx = error_index.split(':');
                                    var error = that.value[idx[0]].errors[idx[1]];
                                    that.app.log.debug('cliccato',error_index,error);
                                    that.errorClicked(error);
                                });
                            }
                        }
                        //.addClass('danger');
                    }
                }
            },
            hasClassError : function (fieldName) {
                var that = this;
                console.log('fieldName',fieldName,that.value[fieldName].errors)
                if (that.value[fieldName].errors)
                    return true;
                return false;
            },
        }
    }
}

Vue.component('csv-dashboard', {
    extends : crud.components.cComponent,
    props: ['cProviderName'],
    components : {
        'v-list-data' : Vue.component('v-list-data',{
            extends : crud.components.views.vList,
            methods : {
                tdClass(index,key) {
                    var that = this;
                    if (that.hasError(index,key))
                        return 'field-' + key + ' ' + that.errorClass;
                    return 'field-' + key;
                },
                hasError(index,key) {
                    var that = this;
                    var errors = that.value[index].errors || [];
                    if (errors.length > 0) {
                        for (var i in errors) {
                            if (key == errors[i].field_name)
                                return true
                        }

                    }
                    return false;
                },
                setSheet(sheetName) {
                    var that = this;
                    console.log('setSheet',sheetName);
                    var params = that.route.getParams();
                    if (sheetName)
                        params['s_datafile_sheet'] = sheetName;
                    else
                        params['s_datafile_sheet'] = '';
                    that.reload();

                },
                setRouteValues : function (route) {
                    var that = this;
                    route.setValues({
                        jobId : this.$parent.jobId,
                        modelName : this.$parent.providerName,
                    });
                    var params = route.getParams();
                    if (that.showError) {
                        params ['datafile_only_errors'] = 1;
                    } else {
                        params['datafile_only_errors'] =  0;
                    }
                    route.setParams(params);
                    return route;
                },
                completed() {
                    console.log('v-list csv completed',this.metadata);
                    this.getComponent('sheetSelect').setDomainValues(this.metadata.sheets,this.metadata.sheets_order);
                    var params = this.route.getParams();
                    var selectedSheetName = params['s_datafile_sheet'];
                    if (selectedSheetName) {
                        this.getComponent('sheetSelect').setValue(Object.values(this.metadata.sheets).indexOf(selectedSheetName));
                    }

                    // this.selectSheetConf.domainValues = this.metadata.sheets;
                    // this.selectSheetConf.domainValuesOrder = this.metadata.sheets_order;
                    this.multiSheets = (this.metadata.sheets_order.length > 0)
                }
            },
            template : '#csv-dashboard-v-list-data-template'
        })
    },
    mounted() {
        var that = this;
        that.$crud.EventBus.$on('start-import',function (params) {
            console.log('event',params);
            that.jobId = params.jobId;
            that.progressEnabled = true;
            that.checkStatus();
        })

    },
    methods : {

        dynamicData(conf) {
            var that = this;
            if (that.cProviderName)
                conf.providerName = that.cProviderName;
            conf.confUpload.modelName = conf.confUpload.modelName || conf.providerName;
            console.log('dynamic data ',conf);
            return conf;
        },

        checkStatus : function () {
            var that = this;
            var r = that.createRoute('status_queue');
            r.setValues({
                id : that.jobId
            });
            Server.route(r,function (json) {
                if (json.error) {
                    that.waitEnd();
                    that.progressEnabled = false;
                    that.errorDialog(json.msg);
                    return ;
                }
                that.progress(json);
            })
        },
        progress : function (json) {
            var that = this;
            var checkError = that.checkJobError(json);

            if (checkError.error ) {
                that.waitEnd();
                that.progressEnabled = false;
                that.errorDialog(checkError.msg);
                return ;
            }
            if (json.job.end) {
                console.log('job end',that.status)
                that.waitEnd();
                that.progressEnabled = false;
                if (that.status == 'loading') {
                    that.status = 'tosave';
                    that.saveEnabled = true;
                    that.uploadEnabled = false;
                    //that.datafileConf.jobId = that.jobId;
                    //that.modelName = that.csvProviderName;
                }
                if (that.status == 'saving') {
                    that.status = 'upload';
                    that.uploadEnabled = true;
                    that.saveEnabled = false;
                }
                console.log('job end 2',that.status,that.saveEnabled,that.uploadEnabled)
                return ;
            }
            setTimeout(that.checkStatus,
                500)
        },

        checkJobError : function (json) {
            var that = this;
            if (json.error) {
                return  {
                    error : 1,
                    msg : json.msg
                };
            }
            if (json.job && json.job.error) {
                return {
                    error : 1,
                    msg : json.job.msg
                };
            }
            return {
                error : 0
            };
        },
        _listConf() {
            var that = this;
            var userConf = that.merge({},that.viewList);
            userConf.modelName = that.providerName;
            return userConf;
        },
        _saveConf() {
            var that = this;
            var userConf = that.merge({},that.viewSave);
            userConf.modelName = that.providerName;
            userConf.customActions = that.viewSave.customActions || {};;
            userConf.fieldsConfig = that.viewSave.fieldsConfig || {};
            var aS = userConf.customActions['action-save-import'] || {};
            aS.csvDashboard = that;
            aS.execute = function () {
                var thatAction = this;
                var vL = thatAction.getComponent('csvViewList');
                var __save = function () {
                    thatAction.waitStart('Salvataggio dati in corso...');
                    var r = thatAction.createRoute('save_datafile');
                    var viewParams = thatAction.view.getViewData();
                    var params = thatAction.merge(viewParams,{
                        datafile_load_id : thatAction.csvDashboard.jobId,
                        datafileProviderName : thatAction.csvDashboard.providerName,
                    })
                    r.setParams(params);
                    Server.route(r,function (json) {
                        if (json.error) {
                            thatAction.waitEnd();
                            thatAction.errorDialog(json.msg);
                            return ;
                        }
                        thatAction.csvDashboard.jobId = json.jobId;
                        thatAction.csvDashboard.status = 'saving';
                        thatAction.csvDashboard.progressEnabled = true;
                        thatAction.csvDashboard.checkStatus();
                    })
                }
                if (vL && vL.metadata.has_datafile_errors) {
                    thatAction.confirmDialog('Il file contiene degli errori, le righe con errore verranno scartate. Salvo Comunque?',{
                        ok() {
                            __save()
                        }

                    })
                    return ;
                }
                __save();
            }
            userConf.customActions['action-save'] = aS;
            userConf.actions.push('action-annulla');
            userConf.customActions['action-annulla'] = {
                text : 'app.annulla',
                execute : function () {
                    that.waitStart('Cancellazione dati...')
                    var r = that.createRoute('datafile_cancel');
                    r.setValues({
                        foorm : that.providerName,
                        foormtype: 'list',
                        constraintField : 'datafile_id',
                        constraintValue : that.jobId,
                    })
                    Server.route(r,function (json) {
                        that.waitEnd();
                        if (json.error) {
                            that.errorDialog(json.msg);
                            return ;
                        }
                        that.status = 'upload';
                        that.uploadEnabled = true;
                        that.saveEnabled = false;
                    })

                }
            }
            return  userConf;
        },
        _uploadConf() {
            var that = this;
            var userConf = that.merge({},that.viewUpload);
            userConf.modelName = that.providerName;
            userConf.customActions = that.viewUpload.customActions || {};
            userConf.fieldsConfig = that.viewUpload.fieldsConfig || {};
            console.log('aaaa',userConf,'viewUpload',that.viewUpload);

            var rsName = that.confUpload.name;
            userConf.fields.push(rsName);
            //userConf.fields.push('resource');
            userConf.fieldsConfig[rsName] = that.confUpload;
            var aS = userConf.customActions['action-save'] || {};
            aS.enabled =  false;
            aS.csvDashboard = that;
            aS.execute = function () {
                var thatAction = this;
                thatAction.csvDashboard.status = 'loading';
                var w = thatAction.view.getWidget('resource');
                var value = JSON.parse(w.getValue());
                var r = thatAction.createRoute('load_datafile');
                var viewParams = thatAction.view.getViewData();
                var params = thatAction.merge(viewParams,{
                    'fileName': value.id,
                    'datafileProviderName': thatAction.csvDashboard.providerName,
                })
                r.setParams(params);
                console.log('ROUTE',r.getConf());
                that.waitStart('Validazione dati in corso...');
                Server.route(r,function (json) {
                    console.log('json',json);
                    var checkError = thatAction.csvDashboard.checkJobError(json);
                    if (checkError.error) {
                        that.waitEnd();
                        thatAction.csvDashboard.status = 'upload';
                        thatAction.errorDialog(checkError.msg);
                        return ;
                    }
                    var params = {
                        jobId : json.jobId,
                        progressEnabled : true,
                    }
                    thatAction.$crud.EventBus.$emit('start-import',params);
                    // return ;
                    // that.jobId = json.jobId;
                    // that.progressEnabled = true;
                    // that.checkStatus();
                })

                // that.confirmDialog('Procedo con l\'importazione del csv?',{
                //     ok : function () {
                //
                //     }
                // })
            }
            userConf.customActions['action-save'] = aS;

            return  userConf;
        }
    },
    template : '#csv-dashboard-template'
});

//routes
crud.routes.load_datafile = {
    method      : "post",
    url         : '/queue/add/datafile/load',
    resultType  : 'record',
    protocol    : 'record',
    extra_params : {}
};

crud.routes.status_queue = {
    method      : "get",
    url         : '/queue/status/{id}',
    resultType  : 'record',
    protocol    : 'record',
    extra_params : {}
};

crud.routes.save_datafile = {
    method      : "post",
    url         : '/queue/add/datafile/save',
    resultType  : 'record',
    protocol    : 'record'
};

crud.routes.datafile_data = {
    method      : "get",
    url         : '/foormc/{modelName}/datafile_id/{jobId}',
    resultType  : 'list',
    protocol    : 'list'
};

// route per eventuali configurazioni dati in ingresso prima dell'upload
crud.routes.datafile_insert = {
    method      : "get",
    url         : '/foorm/{modelName}/new',
    resultType  : 'record',
    protocol    : 'record'
};

// route per eventuali configurazioni dati in ingresso prima del save
crud.routes.datafile_import = {
    method      : "get",
    url         : '/foorm/{modelName}/import/{jobId}',
    resultType  : 'record',
    protocol    : 'record'
};
// route per annullare l'ìmportazione
crud.routes.datafile_cancel = {
    method      : "post",
    url         : '/foormcaction/flush-datafile/{foorm}/{foormtype}/{constraintField}/{constraintValue}',
    resultType  : 'record',
    protocol    : 'record'
};
