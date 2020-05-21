crud.components.cManage = Vue.component('c-manage',{
    extends : crud.components.cComponent,
    props : ['cModel','cInlineEdit'],
    mounted : function() {
        this.createList();
        this.createSearch();
    },
    data : function () {
        var thisManage = this;
        var modelName = thisManage.cModel;
        var modelConf = "Model" + thisManage.pascalCase(modelName);
        var originalConf = window[modelConf]?window[modelConf]:{};
        console.log('modelName',modelName,modelConf,originalConf);
        var searchConf = thisManage.confMerge(thisManage.$crud.conf.search,(originalConf.search || {}));
        var listConf = null;
        var listEditConf = null;
        if (thisManage.cInlineEdit) {
            listEditConf = thisManage.confMerge(thisManage.$crud.conf.listEdit,(originalConf.listEdit || {}));
        } else {
            listConf = thisManage.confMerge(thisManage.$crud.conf.list,(originalConf.list || {}));
        }

        var editConf = thisManage.confMerge(thisManage.$crud.conf.edit,(originalConf.edit || {}));
        var viewConf = thisManage.confMerge(thisManage.$crud.conf.view,(originalConf.view || {}));
        var insertConf = thisManage.confMerge(thisManage.$crud.conf.insert,(originalConf.insert || editConf));
        insertConf.routeName = 'insert';
        searchConf = thisManage.confMerge(searchConf,{
            customActions : {
                'action-search' : {
                    // beforeExecute : function( callback ) {
                    //     var act = this;
                    //     act.confirmDialog('sei sicuro',{
                    //         ok:function () {
                    //             return callback();
                    //         }
                    //     })
                    //
                    //     return true;
                    // },
                    beforeExecute : function() {
                        return true;
                    },
                    execute : function () {
                        var that = this;
                        var formData = that.view.getViewData();
                        thisManage.listComp.route.setParams(formData);
                        thisManage.listComp.reload();
                        return ;
                    },
                    afterExecute: function () {

                    }
                }
            }
        });
        var actionSaveBack = thisManage.merge(thisManage.$crud.collectionActions['action-save'], {
                text : 'Salva e Torna alla lista',
                afterExecute: function () {
                    thisManage.jQe('[c-collapse-edit]').collapse('hide');
                    thisManage.jQe('[c-collapse-list]').collapse('show');
                    this.view.$destroy();
                    thisManage.listComp.reload();
                    thisManage.jQe('[c-edit-container]').html(' ');
                }
            });

        editConf = thisManage.confMerge(editConf,{
            customActions : {
                'action-save-back' : actionSaveBack,
                'action-back' : {
                    beforeExecute : function() {
                        return true;
                    },
                    execute : function () {
                        thisManage.jQe('[c-collapse-edit]').collapse('hide');
                        thisManage.jQe('[c-collapse-list]').collapse('show');
                        this.view.$destroy();
                        thisManage.listComp.reload();
                        thisManage.jQe('[c-edit-container]').html(' ');
                    },
                    afterExecute: function () {
                        //this.$crud.messageDialog('tornato indiestro');
                    }
                }
            }
        });
        if (editConf.actions.indexOf('action-save-back') < 0)
            editConf.actions.push('action-save-back');

        insertConf = thisManage.confMerge(insertConf,{
            customActions : {
                'action-save-back' : actionSaveBack,
                'action-back' : {
                    execute : function () {
                        thisManage.jQe('[c-collapse-edit]').collapse('hide');
                        thisManage.jQe('[c-collapse-list]').collapse('show');
                        this.view.$destroy();
                        thisManage.listComp.reload();
                        thisManage.jQe('[c-edit-container]').html(' ');
                    }
                }
            }
        })
        if (insertConf.actions.indexOf('action-save-back') < 0)
            insertConf.actions.push('action-save-back');
        if (listConf) {
            listConf = thisManage.confMerge(listConf,{
                customActions : {
                    'action-edit' : {
                        execute : function () {
                            var that = this;
                            thisManage.updateTitle = 'Modifica ' + modelName;
                            var id= 'd' + (new Date().getTime());
                            if (thisManage.editComp) {
                                thisManage.editComp.$destroy();
                                thisManage.editComp = null;
                            }
                            thisManage.jQe('[c-edit-container]').html('<div id="'+id+'"></div>');
                            if (editConf.inlineTemplate) {
                                var v = Vue.component(id,{
                                    extends : thisManage.$options.components['v-edit'],
                                    template : jQuery(insertConf.inlineTemplate).html()
                                });


                                thisManage.editComp = new v({
                                    propsData: {
                                        cModel: modelName,
                                        cPk : that.modelData[editConf.primaryKey],
                                        cConf : editConf
                                    }
                                });
                                thisManage.editComp.$mount('#'+id);
                            } else {
                                console.log('EditConf',that.modelData);
                                thisManage.editComp = new that.$options.components['v-edit']({

                                    propsData : {
                                        cModel : modelName,
                                        cPk : that.modelData[editConf.primaryKey],
                                        cConf : editConf
                                    }
                                });
                                thisManage.editComp.$mount('#'+id);
                            }

                            thisManage.jQe('[c-collapse-edit]').collapse('show');
                            thisManage.jQe('[c-collapse-list]').collapse('hide');
                        }
                    },
                    'action-view' : {
                        execute : function () {
                            var that = this;
                            var id= 'd' + (new Date().getTime());
                            if (thisManage.viewComp) {
                                thisManage.viewComp.$destroy();
                                thisManage.viewComp = null;
                            }
                            var pk = that.modelData[viewConf.primaryKey];
                            //console.log('VIEWDATA',pk);
                            //viewConf.pk = pk;
                            thisManage.jQe('[c-view-container]').html('<div id="'+id+'"></div>');
                            thisManage.viewComp  = new that.$options.components['v-view']({
                                propsData : {
                                    cModel : modelName,
                                    cPk : pk,
                                    cConf : viewConf
                                }
                            });
                            thisManage.viewComp.$mount('#'+id);
                            thisManage.jQe('[c-view_dialog]').modal('show');
                        }
                    },
                    'action-insert' : {
                        execute : function () {
                            var that = this;
                            thisManage.updateTitle = 'Inserimento ' + modelName;
                            var id= 'd' + (new Date().getTime());
                            thisManage.jQe('[c-edit-container]').html('<div id="'+id+'"></div>');
                            if (thisManage.insertComp)
                                thisManage.insertComp.$destroy();
                            if (insertConf.inlineTemplate) {
                                var v = Vue.component(id,{
                                    extends : thisManage.$options.components['v-insert'],
                                    template : jQuery(insertConf.inlineTemplate).html()
                                });


                                thisManage.insertComp = new v({
                                    propsData: {
                                        cModel: modelName,
                                        cConf: insertConf
                                    }
                                });
                            } else {
                                thisManage.insertComp = new that.$options.components['v-insert']({
                                    propsData : {
                                        cModel : modelName,
                                        cConf : insertConf
                                    }
                                });
                            }
                            thisManage.insertComp.$mount('#'+id);
                            thisManage.jQe('[c-collapse-edit]').collapse('show');
                            thisManage.jQe('[c-collapse-list]').collapse('hide');
                        }
                    }
                }
            });
        }

        if (listEditConf) {
            listEditConf = thisManage.confMerge(listEditConf,{
                customActions : {
                    'action-view' : {
                        execute : function () {
                            var that = this;
                            var id= 'd' + (new Date().getTime());
                            thisManage.jQe('[c-view-container]').html('<div id="'+id+'"></div>');
                            var v = new that.$options.components['v-view']({
                                propsData : {
                                    cModel : modelName,
                                    cPk : that.modelData[viewConf.primaryKey],
                                    cConf : viewConf
                                }
                            });
                            v.$mount('#'+id);
                            thisManage.jQe('[c-view_dialog]').modal('show');
                        }
                    },
                    'action-insert' : {
                        execute : function () {
                            var that = this;
                            thisManage.updateTitle = 'Inserimento ' + modelName;
                            var id= 'd' + (new Date().getTime());
                            thisManage.jQe('[c-edit-container]').html('<div id="'+id+'"></div>');
                            if (thisManage.insertComp)
                                thisManage.insertComp.$destroy();
                            if (insertConf.inlineTemplate) {
                                var v = Vue.component(id,{
                                    extends : thisManage.$options.components['v-insert'],
                                    template : jQuery(insertConf.inlineTemplate).html()
                                });


                                thisManage.insertComp = new v({
                                    propsData: {
                                        cModel: modelName,
                                        cConf: insertConf
                                    }
                                });
                            } else {
                                thisManage.insertComp = new that.$options.components['v-insert']({
                                    propsData : {
                                        cModel : modelName,
                                        cConf : insertConf
                                    }
                                });
                            }
                            thisManage.insertComp.$mount('#'+id);
                            thisManage.jQe('[c-collapse-edit]').collapse('show');
                            thisManage.jQe('[c-collapse-list]').collapse('hide');
                        }
                    }
                }
            });
        }

        var d = {
            modelName   : modelName,
            listConf    : listConf,
            listEditConf : listEditConf,
            editConf    : editConf,
            insertConf   : insertConf,
            viewConf    : viewConf,
            searchConf : searchConf,
            updateTitle : '',
            listComp : null,
            editComp : null,
            insertComp : null,
            viewComp: null,
            searchComp : null
        }
        return d;
    },

    methods : {
        createList : function () {
            var that = this;
            // monto la lista
            var id= 'd' + (new Date().getTime());
            that.jQe('[c-list-container]').html('<div id="'+id+'"></div>');
            if (that.listComp)
                that.listComp.$destroy();
            if (that.listConf) {
                if (that.listConf.inlineTemplate) {
                    var v = Vue.component(id,{
                        extends : that.$options.components['v-list'],
                        template : jQuery(that.listConf.inlineTemplate).html()
                    });
                    that.listComp = new v({
                        propsData: {
                            cModel: this.cModel,
                            cConf : that.listConf,
                            cRef : 'list-view'
                        }
                    });
                } else {
                    that.listComp = new that.$options.components['v-list']({
                        propsData : {
                            cModel : that.cModel,
                            cConf : that.listConf,
                            cRef : 'list-view'
                        }
                    });
                }
            } else {
                that.listComp = new that.$options.components['v-list-edit']({
                    propsData : {
                        cModel : that.cModel,
                        cConf : that.listEditConf,
                        cRef : 'list-view'
                    }
                });
            }

            that.listComp.$mount('#'+id);
        },
        createSearch : function () {
            var that = this;
            if (that.searchConf.fields.length == 0)
                return ;
            // monto la lista
            var id= 'd' + (new Date().getTime());
            that.jQe('[c-search-container]').html('<div id="'+id+'"></div>');
            if (that.searchComp)
                that.searchComp.$destroy();
            if (that.searchConf.inlineTemplate) {
                var v = Vue.component(id,{
                    extends : that.$options.components['v-search'],
                    template : jQuery(that.searchConf.inlineTemplate).html()
                });
                that.searchComp = new v({
                    propsData: {
                        cModel: that.cModel,
                        cConf : that.searchConf,
                    }
                });
            } else {
                that.searchComp = new that.$options.components['v-search']({
                    propsData : {
                        cModel : that.cModel,
                        cConf : that.searchConf,
                    }
                });
            }
            that.searchComp.$mount('#'+id);
        }
    },
    template : '#c-manage-template'
});
