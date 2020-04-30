Vue.component('dashboard-csv',{
    props : ['c-conf'],
    template : '#dashboard-csv-template',

    data : function () {
       var that = this;
       var d = that.defaultData();
       d.fileName = null;
       d.fileOriginalName = null;
       d.autoupload = d.conf.autoupload?d.conf.autoupload:false;
       return d;
    },

    mounted : function() {
        var that = this;
        this.$root.$on('file_selected', function () {
            that.fileSelected();
        })
    },
    methods : {
        defaultData : function() {
            var _c = this.cConf || {};
            return {
                conf : _c
            }
        },
        fileSelected : function () {
            var that = this;
            var view = that.$refs.uploadView;
            VIEW = view;
            var form = jQuery(view.jQe('form'))[0];
            // Create an FormData object
            var data = new FormData(form);
            console.log('formdata',data);


            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: "/uploadfile",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function (json, textStatus, jqXHR) {
                    //that.crudApp.waitEnd();
                    if (json.error ) {
                        that.crudApp.errorDialog(json.msg);
                        return;
                    }
                    that.fileName = json.result['filename'];
                    that.fileOriginalName = json.result.original_name;

                    //that.dataForm.find('input[name="fileName"]').val(json.result['filename']);

                    if (that.autoload) {
                        that.loadCsv();
                    } else {
                        //jQuery(that.container).find('[data-button_load]').removeClass('hide');
                    }

                    // $("#result").text(data);
                    // console.log("SUCCESS : ", data);
                    // $("#btnSubmit").prop("disabled", false);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    that.crudApp.errorDialog('error ' + textStatus );
                    console.error(errorThrown);
                    // $("#result").text(e.responseText);
                    // console.log("ERROR : ", e);
                    // $("#btnSubmit").prop("disabled", false);

                }
            });

            return ;
        },
        loadCsv: function () {
            var that = this;

            /*
             * risposta in load
             * durante la load
             * {
             *  rows : [] , //righe caricates
             *  current_row : 0,
             *  errors : [{
             *      row : int,
             *      col : int,
             *      errors : [
             *          {key : descrizione},
             *      ]
             *  }]
             */
            //var vConf = that._viewConfig[viewKey];
            //var view = that.getView(viewKey);
            if (!that.fileName) {
                that.crudApp.errorDialog('Devi prima caricare un file dati');
                return ;
            }
            var postData = {
                fileName : that.fileName,
                csvProviderName : that.csvProviderName
            }
            that.crudApp.confirmDialog('Questa operazione potrebbe richiedere minuti, continuare?', {
                ok : function () {
                    that.crudApp.waitStart();
                    // alert('aaa');
                    // return ;
                    var r = new RouteLoadCsv();
                    r.params = postData;
                    Server.route(r, function (json) {
                        that.crudApp.waitEnd();
                        if (parseInt(json.error)) {
                            that.crudApp.errorDialog(json.msg);
                            return;
                        }
                        if (json.job && json.job.error) {
                            that.crudApp.errorDialog(json.job.msg);
                            return;
                        }
                        jQuery(that.container).find('[data-collapse_progress]').collapse('show');
                        jQuery(that.container).find('[data-collapse_upload]').collapse('hide');
                        that.waitQueueJob(json.jobId, "Carico file...", function (waitJson) {
                            that.crudApp.log.debug('load complete',waitJson);
                            if (parseInt(waitJson.error) || (!Array.isArray(waitJson.job) && parseInt(waitJson.job.error))) {
                                var err_msg = waitJson.error ? waitJson.msg : waitJson.job.msg;
                                that.crudApp.errorDialog(err_msg);
                                jQuery(that.container).find('[data-collapse_progress]').collapse('hide');
                                jQuery(that.container).find('[data-collapse_upload]').collapse('show');
                                that._initUploadForm();
                                return;
                            }
                            jQuery(that.container).find('[data-collapse_progress]').collapse('hide');
                            jQuery(that.container).find('[data-collapse_csvdata]').collapse('show');
                            console.log('listKey',that.listKey)
                            that.crudApp.getView(that.listKey).jobId = json.jobId;
                            that.crudApp.renderView(that.listKey);

                        })
                    })
                }
            });

            return ;

        },

        waitQueueJob : function (jobId,msg,callback) {
            var that = this;
            var r = new RouteStatusQueue();
            r.values.id = jobId;
            Server.route(r,function (json) {
                console.log('wait ', json);
                if (parseInt(json.error)) {
                    if (callback) {
                        callback(json);
                    } else {
                        that.crudApp.errorDialog(json.msg);
                    }
                    return ;
                }
                if (json.job.end) {
                    if (callback) {
                        callback(json);
                    } else {
                        if (json.job.error) {
                            that.crudApp.errorDialog(json.job.msg);
                        } else {
                            that.crudApp.messageDialog(json.job.msg);
                        }
                    }
                    return ;
                }
                that.showProgress(msg,json.job.progress);
                setTimeout(function () {
                    that.waitQueueJob(jobId,msg,callback);
                },2000);
            })
        },

        showProgress: function (msg, progress) {
            var that = this;
            var prg = jQuery(that.container).find('[data-csvstatus]');
            if (progress === false || progress === undefined) {
                jQuery(prg).find(".progress").css("width", "100%");
                jQuery(prg).find(".progress-bar").text(" ");
            } else {
                jQuery(prg).find(".progress").css("width", progress + "%");
                jQuery(prg).find(".progress-bar").text(progress + "%");
            }
            jQuery(prg).find('[data-csvstatus_msg]').html(msg);
            jQuery(prg).removeClass('hide');
        },
    }
});