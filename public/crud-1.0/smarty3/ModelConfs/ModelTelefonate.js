function getAllUrlParams2(url) {
    var params = {};
    var tmp = url ? url.split('?') : window.location.href.split("?");


    if (tmp.length != 2)
        return params
    var sparams = tmp[1].split("&");
    for (var i in sparams) {
        var tmp = sparams[i].split("=");
        if (tmp.length != 2)
            continue;
        var name = tmp[0];
        var value = tmp[1];
        if (name.indexOf('[]') >= 0) {
            if (!params[name])
                params[name] = [];
            params[name].push(decodeURIComponent(value))
        } else {
            params[name] = decodeURIComponent(value);
        }

    }
    //console.log('getAllUrlParams',url,params);
    return params;

};

function istitutoID() {
    var params = getAllUrlParams2();
    return params['T_ANAGIST_ID'];
}

var b2SelectIstituti = {
    foormName: 'progetto',
    viewType: 'search',
    labelFields: ['id','T_ANAGIST_DESCBREVE'],
    type: "w-b2-select2",
    name: 'T_ANAGIST_ID',
    //primaryKey : 'T_ANAGIST_ID',
    mounted: function () {
        var that = this;
        var params = getAllUrlParams2();
        console.log('params', params);
        var istituto_id = params['T_ANAGIST_ID'];
        var route = that.createRoute('view');
        route.setValues({
            modelName: 'istituto',
            pk: istituto_id

        })
        console.log('route getValues', route.getValues(), 'conf', route.getConf())
        Server.route(route, function (json) {
            if (json.error) {
                that.errorDialog(json.msg);
                return;
            }
            that.value = istituto_id;
            that.referredData = json.result;
            that.referredData['T_ANAGIST_ID'] = istituto_id;


            var text = that.getLabel(that.referredData);
            var newOption = new Option(text, istituto_id, true, true);
            // Append it to the select
            that.jQe('[c-select2]').append(newOption);


            that.$forceUpdate();
            console.log('refereeddata', that.referredData)
        })
    },
    methods: {
        change: function () {
            //var value = this.value;
            //console.log('value',this.getValue());
            window.location.href = '#page?path=telefonate.html&T_ANAGIST_ID=' + this.getValue();
        }
    }
}
var ManageProgetti = {
    manageHeaderClass : 'bg-warning-soft',
    modelName: 'progetto',
    searchConf: {
        fields: [],
    },
    listComponentName: 'v-list-constraint',
    listConf: {
        cRef: 'listProgetti',
        modelName: 'progetto',
        routeName: 'list-constraint',
        paginator: false,
        constraintKey: 'T_ANAGIST_ID',
        //constraintValue: getAllUrlParams2()['T_ANAGIST_ID'],
        actions: [
            'action-insert-custom',
            'action-edit-custom',
            'action-delete',
        ],
        customActions: {
            'action-insert-custom': {
                text: 'nuovo',
                icon: 'fa fa-plus',
                type: 'collection',
                execute: function () {
                    var that = this;
                    Server.get('/nuovo-progetto-istituto/' + getAllUrlParams2()['T_ANAGIST_ID'], {}, function (json) {
                        if (json.error) {
                            that.errorDialog(json.msg);
                            return;
                        }
                        that.view.reload();
                    })
                }
            },
            'action-edit-custom': {
                icon: 'fa fa-edit',
                type: 'record',
                execute: function () {
                    var that = this;
                    var divId = 'd' + (new Date().getTime());
                    var dlg = null;
                    //alert(that.modelData[that.view.primaryKey])
                    //var dialogComp = new that.$crud.components.views[viewName]({
                    var dialogComp = new that.$options.components['v-edit']({
                        propsData: {
                            cConf: that.confMerge(ManageProgetti.editConf, {
                                modelName: 'progetto',
                                pk: that.modelData[that.view.primaryKey],
                                actions: ['action-save'],
                                customActions: {
                                    'action-save': {
                                        afterExecute: function () {
                                            dialogComp.$destroy();
                                            dlg.hide();
                                            that.view.reload();
                                        }
                                    }
                                },
                            })
                            // cModel : 'progetto',
                            // cPk : that.modelData[that.view.primaryKey]
                        }
                    });

                    // creo la dialog custom
                    dlg = that.customDialog({
                        cContent: '<div id="' + divId + '"></div>',
                        cTitle: 'Modifica Progetto',
                        cBig: true,
                        cCallbacks: {
                            // ok: function () {
                            //     dialogComp.$destroy();
                            //     this.hide();
                            //     that.view.reload();
                            // }
                        }
                    });
                    // visualizzo la view
                    dialogComp.$mount('#' + divId);
                    return dialogComp;


                    // that.createModalView('v-edit',{
                    //     cModel : 'progetto',
                    //     cPk : that.modelData[that.view.primaryKey]
                    // },'modifica progetto');
                    //
                    // return ;
                }
            }
        },
        fields: [
            //'tipo',
            'id',
            'T_TIPOPROGETTO_ID',
            'T_MODALITAPROGETTO_ID',
            'T_PROGETTO_ARRUOLATO',
            'T_PROGETTO_FLAG_RIT',
            'T_MOTNOWEB_ID',
        ],
        fieldsConfig: {
            'id': {
                type: 'w-hidden',
            },
            'T_TIPOPROGETTO_ID': {
                type: 'w-select',
                methods: {
                    change: function () {
                        var that = this;
                        var params = {
                            field: 'T_TIPOPROGETTO_ID',
                            value: that.getValue(),
                            id: that.modelData.id,
                        }
                        that.$crud.cRefs['listProgetti'].setSelect(params);
                    }
                }
            },
            'T_MODALITAPROGETTO_ID': {
                type: 'w-select',
                defaultValue: {
                    id: -1,
                    text: 'Select..'
                },
                methods: {
                    change: function () {
                        var that = this;
                        var params = {
                            field: 'T_MODALITAPROGETTO_ID',
                            value: that.getValue(),
                            id: that.modelData.id,
                        }
                        that.$crud.cRefs['listProgetti'].setSelect(params);
                    }
                }
            },
            'T_PROGETTO_FLAG_RIT': {
                type: "w-swap-smarty",
                modelName: 'progetto',
                //domainValues : {
                //    1 : 'fa fa-circle text-danger',
                //    0 : 'fa fa-circle text-success'
                //}
            },
            'T_PROGETTO_ARRUOLATO': {
                type: 'w-select',
                methods: {
                    change: function () {
                        var that = this;
                        var params = {
                            field: 'T_PROGETTO_ARRUOLATO',
                            value: that.getValue(),
                            id: that.modelData.id,
                        }
                        that.$crud.cRefs['listProgetti'].setSelect(params);
                    }
                }
            },
            'T_MOTNOWEB_ID': {
                type: 'w-select',
                methods: {
                    change: function () {
                        var that = this;
                        var params = {
                            field: 'T_MOTNOWEB_ID',
                            value: that.getValue(),
                            id: that.modelData.id,
                        }
                        that.$crud.cRefs['listProgetti'].setSelect(params);
                    }
                }
            },


            // 'anno' : {
            //     type : "w-belongsto",
            //     fields : [],
            // },
            'motivonoweb': {
                type: "w-belongsto",
                labelFields: [
                    'T_MOTNOWEB_DESC',
                ],
            },
            'tipo': {
                type: "w-belongsto",
                labelFields: [
                    'T_TIPOPROGETTO_NOME',
                ],
            },
            'modalita': {
                type: "w-belongsto",
                labelFields: [
                    'T_MODALITAPROGETTO_DESC',
                ],
            },

        },
        //fields : ['T_ANAGIST_ID'],
        methods: {

            setRouteValues: function (route) {
                var that = this;
                //alert('aa')
                var params = getAllUrlParams2();
                console.log('List Edit conf params', params);
                var istituto_id = params['T_ANAGIST_ID'];
                route.setValues({
                    modelName: 'progetto',
                    constraintKey: 'T_ANAGIST_ID',
                    constraintValue: istituto_id,
                })
            },
            setSelect: function (params) {
                var that = this;
                var r = that.createRoute('set');
                r.setValues({
                    modelName: 'progetto',
                    foormType: 'list'
                })
                r.setParams(params);
                Server.route(r, function (json) {
                    if (json.error) {
                        that.errorDialog(json.msg);
                        that.$crud.cRefs['listProgetti'].reload();
                        return;
                    }

                })
            }
        }
    },
    listEditConf: {
        modelName: 'progetto',
        routeName: 'list-constraint',
        paginator: false,
        constraintKey: 'T_ANAGIST_ID',

        actions: [
            'action-insert',
            'action-edit-mode',
            'action-delete',
        ],
        fields: [
            'tipo',
            'T_TIPOPROGETTO_ID',
            // 'modalita',
            // 'T_PROGETTO_ARRUOLATO',
            // 'T_PROGETTO_FLAG_RIT',
            // 'motivonoweb',
        ],
        fieldsConfigEditMode: {
            'T_TIPOPROGETTO_ID': {
                type: 'w-select'
            },
            'tipo': {
                type: 'w-hidden'
            }
        },
        fieldsConfig: {
            'T_TIPOPROGETTO_ID': 'w-text',
            'T_PROGETTO_FLAG_RIT': {
                type: "w-swap-smarty",
                modelName: 'progetto',
                //domainValues : {
                //    1 : 'fa fa-circle text-danger',
                //    0 : 'fa fa-circle text-success'
                //}
            },
            'T_PROGETTO_ARRUOLATO': {},


            // 'anno' : {
            //     type : "w-belongsto",
            //     fields : [],
            // },
            'motivonoweb': {
                type: "w-belongsto",
                labelFields: [
                    'T_MOTNOWEB_DESC',
                ],
            },
            'tipo': {
                type: "w-belongsto",
                labelFields: [
                    'T_TIPOPROGETTO_NOME',
                ],
            },
            'modalita': {
                type: "w-belongsto",
                labelFields: [
                    'T_MODALITAPROGETTO_DESC',
                ],
            },

        },
        //fields : ['T_ANAGIST_ID'],
        methods: {

            setRouteValues: function (route) {
                var that = this;
                alert('aa')
                var params = getAllUrlParams2();
                console.log('List Edit conf params', params);
                var istituto_id = params['T_ANAGIST_ID'];
                route.setValues({
                    modelName: 'progetto',
                    constraintKey: 'T_ANAGIST_ID',
                    constraintValue: istituto_id,
                })
            }
        }
    },

    editConf: {
        modelName : 'progetto',
        actions : ['action-save','action-back'],
        fields: [
            'T_ANAGIST_ID',
            'T_TIPOPROGETTO_ID',
            'T_MODALITAPROGETTO_ID',
            'T_PROGETTO_FLAG_RIT',
            'T_PROGETTO_ARRUOLATO',
            'T_MOTNOWEB_ID',
            'T_MATERIALE_DATA_SPED',
            'T_MATERIALE_DATA_CONTR',
            'T_MATERIALE_DATA_ARRIVO',
            'T_MATERIALE_FIRMA_ARRIVO',
            'T_MATERIALE_NOTE',
            'T_MATERIALE_DATA_RESTIT',
            'T_MATERIALE_FLAG_ARR_SCUOLA',
            'T_MATERIALE_CLASSI_DETTAGLI',
            'T_MATERIALE_CLASSI_DETTAGLI_ALERT',

        ],
        fieldsConfig: {
            'T_ANAGIST_ID' : {
                type : "w-hidden",
            },
            'T_ANNO_ID' : {
                type : "w-input",
                inputType: "number",
            },
            'T_TIPOPROGETTO_ID' : {
                type : "w-select",
            },
            'T_MODALITAPROGETTO_ID' : {
                type : "w-select",
            },
            'T_PROGETTO_FLAG_RIT' : {
                type : "w-select",
            },
            'T_PROGETTO_ARRUOLATO' : {
                type : "w-select",
            },
            'T_MOTNOWEB_ID' : {
                type : "w-select",
            },
            'T_MATERIALE_DATA_SPED' : {
                type : "w-input",
                inputType : "date",
            },
            'T_MATERIALE_DATA_CONTR' : {
                type : "w-input",
                inputType : "date",
            },
            'T_MATERIALE_DATA_ARRIVO' : {
                type : "w-input",
                inputType : "date",
            },
            'T_MATERIALE_FIRMA_ARRIVO' : {
                type : "w-input",
            },
            'T_MATERIALE_NOTE' : {
                type : "w-textarea",
            },
            'T_MATERIALE_DATA_RESTIT' : {
                type : "w-input",
                inputType : "date",
            },
            'T_MATERIALE_FLAG_ARR_SCUOLA' : {
                type : "w-select",
            },
            'T_MATERIALE_CLASSI_DETTAGLI' : {
                type : "w-textarea",
            },
            'T_MATERIALE_CLASSI_DETTAGLI_ALERT' : {
                type : "w-select",
            },

        }
    },

};

var ManageStoricoContatti = {
    manageHeaderClass : 'bg-success-soft',
    modelName: 'storico_contatti',
    searchConf: {
        fields: [],
    },
    listConf : {
        cRef: 'listStoricoContatti',
        headerClass : 'p-0',
        langContext : 'storico_contatti',
        viewTitle : '',
        modelName : 'storico_contatti',
        actions : [],
        paginator : false,
        mounted : function() {
            var that = this;
            var params = getAllUrlParams2();
            console.log('listStoricoContatti params', params);
            var istituto_id = params['T_ANAGIST_ID'];
            that.constraintValue = istituto_id;
        },
        constraintKey: 'T_ANAGIST_ID',
        constraintValue: 1,
        fields: [
            'anno',
            // 'progetto',
            'T_STORCONT_FLAG_CONT',
            'T_STORCONT_FLAG_PART',
            'T_STORCONT_FLAG_ACC',
            'T_STORCONT_FLAG_SPED',
            'T_STORCONT_FLAG_RIT',
            'T_STORCONT_PROGETTO_DESC',
            'T_STORCONT_NOTE_CONTATTO',
            'T_STORCONT_CLASSI_SOMM',
            'T_STORCONT_NOTE_SOMM',
            'T_STORCONT_FLAG_QST_PREV',
            'T_STORCONT_TIPOQUEST',

        ],
        fieldsConfig: {
            'T_STORCONT_FLAG_CONT': {
                type: "w-custom",
                mounted: function () {
                    var that = this;
                    that.value = that.value ? "S" : "N";
                }
            },
            'T_STORCONT_FLAG_PART': {
                type: "w-custom",
                mounted: function () {
                    var that = this;
                    that.value = that.value ? "S" : "N";
                }
            },
            'T_STORCONT_FLAG_ACC': {
                type: "w-custom",
                mounted: function () {
                    var that = this;
                    that.value = that.value ? "S" : "N";
                }
            },
            'T_STORCONT_FLAG_SPED': {
                type: "w-custom",
                mounted: function () {
                    var that = this;
                    that.value = that.value ? "S" : "N";
                }
            },
            'T_STORCONT_FLAG_RIT': {
                type: "w-custom",
                mounted: function () {
                    var that = this;
                    that.value = that.value ? "S" : "N";
                }
            },
            'T_STORCONT_NOTE_CONTATTO': {},
            // 'T_STORCONT_CLASSI_SOMM' : {
            //
            // },
            // 'T_STORCONT_NOTE_SOMM' : {
            //
            // },
            // 'T_STORCONT_FLAG_QST_PREV' : {
            //     type : "w-custom",
            //     mounted : function() {
            //         var that = this;
            //         that.value = that.value ? "S" : "N";
            //     }
            // },
            'T_STORCONT_PROGETTO_DESC': {
                type: "w-text",
            },
            'T_STORCONT_TIPOQUEST': {
                type: "w-text",
            },
            'istituto': {
                type: "w-belongsto",
                labelFields: [
                    'T_ANAGIST_DESCBREVE'
                ],
            },
            'anno': {
                type: "w-belongsto",
                labelFields: [
                    'T_ANNO_DESC',
                ],
            },
            // 'progetto' : {
            //     type : "w-belongsto",
            //     labelFields : [
            //         'T_PROGETTO'
            //     ],
            // },

        },


    },


}

var ModelTelefonata = {
    insert: {},
    edit: {
        modelName: 'telefonata',
        actions: ['action-save', 'action-back'],
        fields: [
            //'T_TELEFONATA_PROGRESSIVO',
            'T_ANAGIST_ID',
            'T_TELEFONATA_DATA',
            'T_TELEFONATA_DATA_RICHIAMO',

        ],
        fieldsConfig: {
            'T_ANAGIST_ID': {
                type: "w-hidden",
                inputType: "number",
            },
            'T_TELEFONATA_DATA': {
                type: "w-input",
                inputType: 'date',
            },
            'T_TELEFONATA_DATA_RICHIAMO': {
                type: "w-input",
                inputType: 'datetime-local'
            },

        }

    },
    list: {
        viewTitle: 'Telefonate',
        actions: ['action-insert', 'action-delete', 'action-edit'],
        fields: [
            // 'T_TELEFONATA_PROGRESSIVO',
            'T_TELEFONATA_DATA',
            'T_TELEFONATA_DATA_RICHIAMO',
        ],
        fieldsConfig: {
            'T_TELEFONATA_DATA': {
                mounted: function () {
                    var that = this;
                    if (that.value)
                        that.value = moment(that.value).format('DD-MM-YYYY');

                }
            },
            'T_TELEFONATA_DATA_RICHIAMO': {
                mounted: function () {
                    var that = this;
                    if (that.value)
                        that.value = moment(that.value).format('DD-MM-YYYY HH:mm');

                }
            }
        },
        customActions: {
            'action-insert': {
                text: 'Nuova',
                icon: 'fa fa-phone',
                css: 'btn-outline-success',
                execute: function () {
                    var that = this;
                    var divId = 'd' + (new Date().getTime());
                    var dlg = null;
                    //alert(that.modelData[that.view.primaryKey])
                    //var dialogComp = new that.$crud.components.views[viewName]({
                    var dialogComp = new that.$options.components['v-insert']({
                        propsData: {
                            cConf: that.confMerge(ModelTelefonata.edit, {
                                actions: ['action-save'],
                                modelName: 'telefonata',
                                routeName: 'insert',
                                customActions: {
                                    'action-save': {
                                        afterExecute: function () {
                                            dialogComp.$destroy();
                                            dlg.hide();
                                            that.view.reload();
                                        }
                                    }
                                },
                                methods: {
                                    completed: function () {
                                        //console.log('telefonata insert',this._uid);
                                        this.getWidget('T_ANAGIST_ID').setValue(getAllUrlParams2()['T_ANAGIST_ID'])

                                    }
                                }
                            })
                            // cModel : 'progetto',
                            // cPk : that.modelData[that.view.primaryKey]
                        }
                    });

                    // creo la dialog custom
                    dlg = that.customDialog({
                        cContent: '<div id="' + divId + '"></div>',
                        cTitle: 'Inserisci telefonata',
                        cBig: true,
                        cCallbacks: {
                            // ok: function () {
                            //     dialogComp.$destroy();
                            //     this.hide();
                            //     that.view.reload();
                            // }
                        }
                    });
                    // visualizzo la view
                    dialogComp.$mount('#' + divId);
                    return dialogComp;
                }
            },
            'action-edit': {
                execute: function () {
                    var that = this;
                    var divId = 'd' + (new Date().getTime());
                    var dlg = null;
                    //alert(that.modelData[that.view.primaryKey])
                    //var dialogComp = new that.$crud.components.views[viewName]({
                    var dialogComp = new that.$options.components['v-edit']({
                        propsData: {
                            cConf: that.confMerge(ModelTelefonata.edit, {
                                actions: ['action-save'],
                                modelName: 'telefonata',
                                pk: that.modelData.id,
                                customActions: {
                                    'action-save': {
                                        afterExecute: function () {
                                            dialogComp.$destroy();
                                            dlg.hide();
                                            that.view.reload();
                                        }
                                    }
                                },
                                // methods : {
                                //     completed : function () {
                                //         //console.log('telefonata insert',this._uid);
                                //         this.getWidget('T_ANAGIST_ID').setValue(getAllUrlParams2()['T_ANAGIST_ID'])
                                //
                                //     }
                                // }
                            })
                            // cModel : 'progetto',
                            // cPk : that.modelData[that.view.primaryKey]
                        }
                    });

                    // creo la dialog custom
                    dlg = that.customDialog({
                        cContent: '<div id="' + divId + '"></div>',
                        cTitle: 'Modifica telefonata',
                        cBig: true,
                        cCallbacks: {
                            // ok: function () {
                            //     dialogComp.$destroy();
                            //     this.hide();
                            //     that.view.reload();
                            // }
                        }
                    });
                    // visualizzo la view
                    dialogComp.$mount('#' + divId);
                    return dialogComp;
                }
            }
        },
        paginator: false,
        mounted: function () {
            var that = this;
            var params = getAllUrlParams2();
            //console.log('ModelTelefonata params',params);
            var istituto_id = params['T_ANAGIST_ID'];
            that.constraintValue = istituto_id;
        },
        constraintKey: 'T_ANAGIST_ID',
        constraintValue: 1,
        // customActions : {
        //     'action-insert' : {
        //         execute : function () {
        //
        //         }
        //     }
        // }
    },
    listStoricoContatti : {
        langContext : 'storico_contatti',
        viewTitle : 'Storico Contatti',
        modelName : 'storico_contatti',
        actions : [],
        paginator : false,
        mounted : function() {
            var that = this;
            var params = getAllUrlParams2();
            console.log('listStoricoContatti params', params);
            var istituto_id = params['T_ANAGIST_ID'];
            that.constraintValue = istituto_id;
        },
        constraintKey: 'T_ANAGIST_ID',
        constraintValue: 1,
        fields: [
            'anno',
            // 'progetto',
            'T_STORCONT_FLAG_CONT',
            'T_STORCONT_FLAG_PART',
            'T_STORCONT_FLAG_ACC',
            'T_STORCONT_FLAG_SPED',
            'T_STORCONT_FLAG_RIT',
            'T_STORCONT_NOTE_CONTATTO',
            'T_STORCONT_CLASSI_SOMM',
            'T_STORCONT_NOTE_SOMM',
            'T_STORCONT_FLAG_QST_PREV',
            'T_STORCONT_PROGETTO_DESC',
            'T_STORCONT_TIPOQUEST',

        ],
        fieldsConfig: {
            'T_STORCONT_FLAG_CONT': {
                type: "w-custom",
                mounted: function () {
                    var that = this;
                    that.value = that.value ? "S" : "N";
                }
            },
            'T_STORCONT_FLAG_PART': {
                type: "w-custom",
                mounted: function () {
                    var that = this;
                    that.value = that.value ? "S" : "N";
                }
            },
            'T_STORCONT_FLAG_ACC': {
                type: "w-custom",
                mounted: function () {
                    var that = this;
                    that.value = that.value ? "S" : "N";
                }
            },
            'T_STORCONT_FLAG_SPED': {
                type: "w-custom",
                mounted: function () {
                    var that = this;
                    that.value = that.value ? "S" : "N";
                }
            },
            'T_STORCONT_FLAG_RIT': {
                type: "w-custom",
                mounted: function () {
                    var that = this;
                    that.value = that.value ? "S" : "N";
                }
            },
            'T_STORCONT_NOTE_CONTATTO': {},
            // 'T_STORCONT_CLASSI_SOMM' : {
            //
            // },
            // 'T_STORCONT_NOTE_SOMM' : {
            //
            // },
            // 'T_STORCONT_FLAG_QST_PREV' : {
            //     type : "w-custom",
            //     mounted : function() {
            //         var that = this;
            //         that.value = that.value ? "S" : "N";
            //     }
            // },
            'T_STORCONT_PROGETTO_DESC': {
                type: "w-text",
            },
            'T_STORCONT_TIPOQUEST': {
                type: "w-text",
            },
            'istituto': {
                type: "w-belongsto",
                labelFields: [
                    'T_ANAGIST_DESCBREVE'
                ],
            },
            'anno': {
                type: "w-belongsto",
                labelFields: [
                    'T_ANNO_DESC',
                ],
            },
            // 'progetto' : {
            //     type : "w-belongsto",
            //     labelFields : [
            //         'T_PROGETTO'
            //     ],
            // },

        },


    },

    istitutoInfo : {
        modelName : 'istituto',
        widgetTemplate : 'tpl-no',
        fieldsConfig : {
            contatto : {
                type : 'w-hasone'
            }
        },
        actions : ['action-edit'],
        customActions : {
            'action-edit' : {
                execute : function () {
                    var that = this;
                    var divId = 'd' + (new Date().getTime());
                    var dlg = null;
                    var dialogComp = new that.$options.components['v-edit']({
                        propsData: {
                            cConf: that.confMerge(ModelIstituto.edit, {
                                actions: ['action-save'],
                                modelName: 'istituto',
                                pk: that.modelData.id,
                                customActions: {
                                    'action-save': {
                                        afterExecute: function () {
                                            dialogComp.$destroy();
                                            dlg.hide();
                                            that.view.reload();
                                        }
                                    }
                                },
                                // methods : {
                                //     completed : function () {
                                //         //console.log('telefonata insert',this._uid);
                                //         this.getWidget('T_ANAGIST_ID').setValue(getAllUrlParams2()['T_ANAGIST_ID'])
                                //
                                //     }
                                // }
                            })
                            // cModel : 'progetto',
                            // cPk : that.modelData[that.view.primaryKey]
                        }
                    });

                    // creo la dialog custom
                    dlg = that.customDialog({
                        cContent: '<div id="' + divId + '"></div>',
                        cTitle: 'Modifica Istiuto',
                        cBig: true,
                        cCallbacks: {
                            // ok: function () {
                            //     dialogComp.$destroy();
                            //     this.hide();
                            //     that.view.reload();
                            // }
                        }
                    });
                    // visualizzo la view
                    dialogComp.$mount('#' + divId);
                    return dialogComp;
                }
            }
        }
        // configurazione nella pagina telefonate dell'istituto selezionato
    }
};
