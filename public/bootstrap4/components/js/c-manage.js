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
                        //this.$crud.messageDialog('tornato indiestro');
                    }
                }
            }
        });

        editConf = thisManage.confMerge(editConf,{
            customActions : {
                'action-back' : {
                    beforeExecute : function() {
                        return true;
                        //var s =  confirm('Sei sicuro?');
                        //console.log('risposta',s);
                        //return s;
                    },
                    execute : function () {
                        jQuery('[c-collapse-edit]').collapse('hide');
                        jQuery('[c-collapse-list]').collapse('show');
                        this.view.$destroy();
                        thisManage.listComp.reload();
                        jQuery('[c-edit-container]').html(' ');
                    },
                    afterExecute: function () {
                        //this.$crud.messageDialog('tornato indiestro');
                    }
                }
            }
        });

        insertConf = thisManage.confMerge(insertConf,{
            customActions : {
                'action-back' : {
                    execute : function () {
                        jQuery('[c-collapse-edit]').collapse('hide');
                        jQuery('[c-collapse-list]').collapse('show');
                        this.view.$destroy();
                        jQuery('[c-edit-container]').html(' ');
                    }
                }
            }
        })

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
                            jQuery('[c-edit-container]').html('<div id="'+id+'"></div>');
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

                            jQuery('[c-collapse-edit]').collapse('show');
                            jQuery('[c-collapse-list]').collapse('hide');
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
                            jQuery('[c-view-container]').html('<div id="'+id+'"></div>');
                            thisManage.viewComp  = new that.$options.components['v-view']({
                                propsData : {
                                    cModel : modelName,
                                    cPk : pk,
                                    cConf : viewConf
                                }
                            });
                            thisManage.viewComp.$mount('#'+id);
                            jQuery('[c-view_dialog]').modal('show');
                        }
                    },
                    'action-insert' : {
                        execute : function () {
                            var that = this;
                            thisManage.updateTitle = 'Inserimento ' + modelName;
                            var id= 'd' + (new Date().getTime());
                            jQuery('[c-edit-container]').html('<div id="'+id+'"></div>');
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
                            jQuery('[c-collapse-edit]').collapse('show');
                            jQuery('[c-collapse-list]').collapse('hide');
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
                            jQuery('[c-view-container]').html('<div id="'+id+'"></div>');
                            var v = new that.$options.components['v-view']({
                                propsData : {
                                    cModel : modelName,
                                    cPk : that.modelData[viewConf.primaryKey],
                                    cConf : viewConf
                                }
                            });
                            v.$mount('#'+id);
                            jQuery('[c-view_dialog]').modal('show');
                        }
                    },
                    'action-insert' : {
                        execute : function () {
                            var that = this;
                            thisManage.updateTitle = 'Inserimento ' + modelName;
                            var id= 'd' + (new Date().getTime());
                            jQuery('[c-edit-container]').html('<div id="'+id+'"></div>');
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
                            jQuery('[c-collapse-edit]').collapse('show');
                            jQuery('[c-collapse-list]').collapse('hide');
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
            jQuery('[c-list-container]').html('<div id="'+id+'"></div>');
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
            jQuery('[c-search-container]').html('<div id="'+id+'"></div>');
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
