Vue.component('csv-dashboard', {
    extends : crud.components.cComponent,
    props: ['cProviderName'],
    data: function () {
        var csvD = this;
        var d = {
            jobId : null,
            progressValue : 20,
            saveEnabled : false,
            uploadEnabled : true,
            progressEnabled : false,
            status : 'upload',
            confUpload : {
                "value": null,
                "name": "resource",

                "maxFileSize": "2M",
                "extensions": [
                    "csv",
                ],
                "ajaxFields": {
                    "resource_type": "attachment"
                },
            },
            saveAction : {
                text : 'Importa Csv',
                execute : function () {
                    var that = this;
                    var r = Route.factory('save_datafile');
                    r.params = {
                        datafile_load_id : csvD.jobId,
                        datafileProviderName : csvD.cProviderName,
                    }
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
            },
            datafileConf : csvD.confMerge(csvD.$crud.conf.list,{
                routeName : 'datafile_data',
                actions : []
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
                    var r = Route.factory('load_datafile');
                    r.params = {
                        'fileName': value.id,
                        'datafileProviderName': that.cProviderName,
                    };
                    console.log('ROUTE',r);
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
            var r = Route.factory('status_queue');
            r.values = {
                id : that.jobId
            }
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
            console.log('PROGRESS',json);
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
        }
    },
    template : '#csv-dashboard-template'
});

//routes
crud.routes.load_data_file = {
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
    url         : '/foormc/datafile_{modelName}/datafile_id/{jobId}',
    resultType  : 'list',
    protocol    : 'list'
};
