Vue.component('csv-dashboard', {
    extends : crud.components.cComponent,
    props: ['cProviderName'],
    components : {
        'v-list-data' : Vue.component('v-list-data',{
            extends : crud.components.views.vList,
            methods : {
                setRouteValues : function (route) {
                    route.setValues({
                        jobId : this.$parent.jobId,
                        modelName : this.$parent.cProviderName,
                    });
                    return route;
                }
            }
        })
    },
    data: function () {
        var csvD = this;
        CSVD = this;
        console.log('this.options',csvD.$options.components);
        crud.components.actions.actionSaveImport = csvD.$options.components['action-save-import'];
        var d = {
            jobId : null,
            progressValue : 20,
            saveEnabled : false,
            uploadEnabled : true,
            progressEnabled : false,
            status : 'upload',
            // confUpload : {
            //     "value": null,
            //     "name": "resource",
            //     "modelName" : csvD.cProviderName,
            //     "maxFileSize": "2M",
            //     "extensions": [
            //         "csv",
            //     ],
            //     "ajaxFields": {
            //         resource_type: "attachment",
            //         field : 'dizionario_comune',
            //     },
            // },
            datafileConf : csvD.mergeConfView(csvD.$crud.conf.list,{
                routeName : 'datafile_data',
                actions : ['action-save-import'],
                customActions : {
                    'action-save-import' : {
                        extends : crud.components.actions.actionBase,
                        text : 'Salva Csv Caricato',
                        css : 'btn bnt-outline-secondary btn-info',
                        type : 'collection',
                        execute : function () {
                            var that = this;
                            var r = that.createRoute('save_datafile');
                            r.setParams({
                                datafile_load_id : csvD.jobId,
                                datafileProviderName : csvD.cProviderName,
                            });
                            Server.route(r,function (json) {
                                if (json.error) {
                                    that.errorDialog(json.msg);
                                    return ;
                                }
                                csvD.jobId = json.jobId;
                                csvD.status = 'saving';
                                csvD.progressEnabled = true;
                                csvD.checkStatus();
                            })
                        }
                    }
                }
            })
        }
        return d;
    },
    methods : {
        uploadsuccess : function (uploadComp) {
            var that = this;
            console.log('value',uploadComp);
            that.confirmDialog('Procedo con l\'importazione del csv?',{
                ok : function () {
                    that.status = 'loading';
                    var value = JSON.parse(uploadComp.value);
                    var r = that.createRoute('load_datafile');
                    r.setParams({
                        'fileName': value.id,
                        'datafileProviderName': that.cProviderName,
                    });
                    console.log('ROUTE',r.getConf());
                    Server.route(r,function (json) {
                        console.log('json',json);
                        var checkError = that.checkJobError(json);
                        if (checkError.error) {
                            that.status = 'upload';
                            that.errorDialog(checkError.msg);
                            return ;
                        }
                        that.jobId = json.jobId;
                        that.progressEnabled = true;
                        that.checkStatus();
                    })
                }
            })
        },
        checkStatus : function () {
            var that = this;
            var r = that.createRoute('status_queue');
            r.setValues({
                id : that.jobId
            });
            Server.route(r,function (json) {
                if (json.error) {
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
                that.progressEnabled = false;
                that.errorDialog(checkError.msg);
                return ;
            }
            if (json.job.end) {
                that.progressEnabled = false;
                if (that.status == 'loading') {
                    that.status = 'tosave';
                    that.saveEnabled = true;
                    that.uploadEnabled = false;
                    that.datafileConf.jobId = that.jobId;
                    that.modelName = that.csvProviderName;
                }
                if (that.status == 'saving') {
                    that.status = 'upload';
                    that.uploadEnabled = true;
                    that.saveEnabled = false;
                }

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
        _loadConf : function() {
            var that = this;
            let conf = null;
            var d = {};
            var providerName = that.cProviderName;
            if (that.cConf) {
                if (typeof that.cConf === 'string' || that.cConf instanceof String) {
                    conf = this.$crud.getDescendantProp(window, that.cConf);
                    if (!conf) {
                        conf = this.$crud.getDescendantProp(this.$crud.conf, that.cConf);
                    }
                } else
                    conf = that.cConf;
            } else if (providerName && window[that.pascalCase(providerName)] ) {
                if (window[that.pascalCase(providerName)]) {
                    conf = window[that.pascalCase(providerName)];
                }
            }
            if (!conf) {
                console.trace();
                throw "Nessuna configurazione trovata per questa csv-dashboard " + that.pascalCase(providerName);
            }

            for (var k in conf) {
                if (k == 'methods')
                    continue;
                d[k] = conf[k];
            }
            d.conf = conf;
            console.log('finalConf', conf);
            return d;
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
