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
                text : 'app.importa-csv'
            }
        }
    },
    viewSave : {
        methods : {
            setRouteValues : function (route) {
                route.setValues({
                    jobId : this.$parent.jobId,
                    modelName : this.$parent.providerName,
                });
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
        routeName : 'datafile_data',
        actions : [],
    }
}

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
                        modelName : this.$parent.providerName,
                    });
                    return route;
                }
            }
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
                console.log('job end',that.status)
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
                var r = thatAction.createRoute('save_datafile');
                var viewParams = thatAction.view.getViewData();
                var params = thatAction.merge(viewParams,{
                    datafile_load_id : thatAction.csvDashboard.jobId,
                    datafileProviderName : thatAction.csvDashboard.providerName,
                })
                r.setParams(params);
                Server.route(r,function (json) {
                    if (json.error) {
                        thatAction.errorDialog(json.msg);
                        return ;
                    }
                    thatAction.csvDashboard.jobId = json.jobId;
                    thatAction.csvDashboard.status = 'saving';
                    thatAction.csvDashboard.progressEnabled = true;
                    thatAction.csvDashboard.checkStatus();
                })
            }
            userConf.customActions['action-save'] = aS;

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
                Server.route(r,function (json) {
                    console.log('json',json);
                    var checkError = thatAction.csvDashboard.checkJobError(json);
                    if (checkError.error) {
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
