crud.conf['c-manage'] = {
    listComponentName : 'v-list',
    searchComponentName :'v-search',
    listEditComponentName: 'v-list-edit',
    editComponentName : 'v-edit',
    insertComponentName: 'v-insert',
    viewComponentName: 'v-view',


    listComp : null,
    searchComp: null,
    listEditComp: null,
    editComp: null,
    insertComp: null,
    viewComp: null,

    layoutGradientColor : null,
    manageHeaderClass : null,
    manageHeaderTextClass : 'text-dark',
    updateTitle: '',
    viewTitle: '',
}
crud.components.cManage = Vue.component('c-manage', {
    extends: crud.components.cComponent,
    props: ['cModel', 'cInlineEdit','cCollapsible'],
    mounted: function () {
        this.createList();
        this.createSearch();
    },
    beforeDestroy() {
        if (this.listComp) this.listComp.$destroy();
        if (this.listEditComp) this.listEditComp.$destroy();
        if (this.editComp) this.editComp.$destroy();
        if (this.searchComp) this.searchComp.$destroy();
        if (this.viewComp) this.viewComp.$destroy();
        if (this.insertComp) this.insertComp.$destroy();
    },

    // data: function () {
    //     var thisManage = this;
    //     var _conf = this._getConf() || {};
    //
    //     var modelName = thisManage.cModel ? thisManage.cModel : _conf.modelName;
    //     if (!modelName)
    //         throw 'model Name not found!';
    //     //console.log('CONF MANAGE',_conf);
    //     var d = {
    //         layoutGradientColor: crud.layoutGradientColor,
    //         modelName: modelName,
    //         updateTitle: '',
    //         viewTitle: '',
    //     };
    //     d.manageHeaderClass = _conf.manageHeaderClass || null;
    //     d.manageHeaderTextClass = _conf.manageHeaderTextClass || 'text-dark';
    //     console.log(modelName , thisManage.cCollapsible);
    //     var collapsibleElement = (thisManage.cCollapsible !== undefined) ? thisManage.cCollapsible :
    //         (_conf.collapsible !== undefined) ? _conf.collapsible :
    //         true;
    //     d.collapsible = (collapsibleElement === true || collapsibleElement === 'collapsed');
    //     d.collapsed =  (collapsibleElement === 'collapsed');
    //     d.collapseId = _conf.collapseId || 'manageCollapse'+modelName;
    //
    //     d = Object.assign(d, thisManage._getListConfiguration(_conf, modelName))
    //     d = Object.assign(d, thisManage._getSearchConfiguration(_conf, modelName))
    //     d = Object.assign(d, thisManage._getListEditConfiguration(_conf, modelName))
    //     d = Object.assign(d, thisManage._getEditConfiguration(_conf, modelName))
    //     d = Object.assign(d, thisManage._getInsertConfiguration(_conf, modelName))
    //     d = Object.assign(d, thisManage._getViewConfiguration(_conf, modelName))
    //     //console.log('ddddd',d);
    //     return d;
    // },

    methods: {
        dynamicData(conf) {
            var thisManage = this;
            //var _conf = this._getConf() || {};

            if (!conf.modelName)
                conf.modelName = thisManage.cModel ? thisManage.cModel : null;
            if (!conf.modelName)
                throw 'model Name not found!';
            conf.layoutGradientColor = crud.layoutGradientColor;

            console.log(conf.modelName , thisManage.cCollapsible);
            var collapsibleElement = (thisManage.cCollapsible !== undefined) ? thisManage.cCollapsible :
                (conf.collapsible !== undefined) ? conf.collapsible :
                    true;
            conf.collapsible = (collapsibleElement === true || collapsibleElement === 'collapsed');
            conf.collapsed =  (collapsibleElement === 'collapsed');
            conf.collapseId = conf.collapseId || 'manageCollapse'+conf.modelName;

            conf = thisManage._getListConfiguration(conf);
            conf = thisManage._getSearchConfiguration(conf);
            conf = thisManage._getListEditConfiguration(conf);
            conf = thisManage._getEditConfiguration(conf);
            conf = thisManage._getInsertConfiguration(conf);
            conf = thisManage._getViewConfiguration(conf);

            //conf = Object.assign(conf, thisManage._getListConfiguration(conf, conf.modelName))
            //conf = Object.assign(conf, thisManage._getSearchConfiguration(conf, conf.modelName))
            //conf = Object.assign(conf, thisManage._getListEditConfiguration(conf, conf.modelName))
            //conf = Object.assign(conf, thisManage._getEditConfiguration(conf, conf.modelName))
            //conf = Object.assign(conf, thisManage._getInsertConfiguration(conf, conf.modelName))
            // conf = Object.assign(conf, thisManage._getViewConfiguration(conf, conf.modelName))
            console.log('CONF MANAGE',conf);
            return conf;
        },
        createList: function () {
            var that = this;
            if (that.listComp)
                that.listComp.$destroy();
            // monto la lista
            var id = 'd' + (new Date().getTime());
            that.jQe('[c-list-container]').html('<div id="' + id + '"></div>');
            if (that.listConf) {
                // if (that.listConf.inlineTemplate) {
                //     var v = Vue.component(id,{
                //         extends : that.$options.components[that.listComponentName],
                //         template : jQuery(that.listConf.inlineTemplate).html()
                //     });
                //     that.listComp = new v({
                //         propsData: {
                //             cModel: that.modelName,
                //             cConf : that.listConf,
                //             cRef : 'list-view'
                //         }
                //     });
                // } else {
                that.listComp = new that.$options.components[that.listComponentName]({
                    propsData: {
                        cModel: that.modelName,
                        cConf: that.listConf,
                        cRef: 'list-view'
                    }
                });
                //}
            } else {
                that.listComp = new that.$options.components[that.listEditComponentName]({
                    propsData: {
                        cModel: that.modelName,
                        cConf: that.listEditConf,
                        cRef: 'list-view'
                    }
                });
            }

            that.listComp.$mount('#' + id);
        },
        createSearch: function () {
            var that = this;
            if (that.searchConf.fields.length == 0)
                return;
            if (that.searchComp)
                that.searchComp.$destroy();
            // monto la search
            var id = 'd' + (new Date().getTime());
            that.jQe('[c-search-container]').html('<div id="' + id + '"></div>');
            // if (that.searchConf.inlineTemplate) {
            //     var v = Vue.component(id,{
            //         extends : that.$options.components[that.searchComponentName],
            //         template : jQuery(that.searchConf.inlineTemplate).html()
            //     });
            //     that.searchComp = new v({
            //         propsData: {
            //             cModel: that.cModel,
            //             cConf : that.searchConf,
            //         }
            //     });
            // } else {
            that.searchComp = new that.$options.components[that.searchComponentName]({
                propsData: {
                    cModel: that.cModel,
                    cConf: that.searchConf,
                }
            });
            //}
            that.searchComp.$mount('#' + id);
        },
        _createEdit: function (action) {
            var thisManage = this;
            if (thisManage.editComp) {
                thisManage.editComp.$destroy();
                thisManage.editComp = null;
            }
            var pkTranslation = thisManage.translate(thisManage.modelName + "." + thisManage.editConf.primaryKey + '.label');

            if (thisManage.modelName === 'istituto') {

                thisManage.updateTitle = 'Modifica ' + thisManage.translate(thisManage.modelName+'.label') + ' (' +
                    pkTranslation +
                    ':' + action.modelData[thisManage.editConf.primaryKey] + ')';
            } else {
                thisManage.updateTitle = 'Modifica ' + thisManage.translate(thisManage.modelName+'.label');
            }

            var id = 'd' + (new Date().getTime());
            thisManage.jQe('[c-edit-container]').html('<div id="' + id + '"></div>');
            thisManage.editComp = new thisManage.$options.components[thisManage.editComponentName]({

                propsData: {
                    cModel: thisManage.modelName,
                    cPk: action.modelData[thisManage.editConf.primaryKey],
                    cConf: thisManage.editConf
                }
            });
            thisManage.editComp.$mount('#' + id);
            thisManage.jQe('[c-collapse-edit]').collapse('show');
            thisManage.jQe('[c-collapse-list]').collapse('hide');
        },
        _createView: function (action) {
            var thisManage = this;
            //var that = this;
            var id = 'd' + (new Date().getTime());

            var pkTranslation = thisManage.translate(thisManage.modelName + "." + thisManage.viewConf.primaryKey + '.label');
            thisManage.viewTitle = thisManage.translate("model." + thisManage.modelName, 0) + ' (' +
                pkTranslation +
                ':' + action.modelData[thisManage.viewConf.primaryKey] + ')';

            if (thisManage.viewComp) {
                thisManage.viewComp.$destroy();
                thisManage.viewComp = null;
            }
            var pk = action.modelData[thisManage.viewConf.primaryKey];
            //console.log('VIEWDATA',pk);
            //viewConf.pk = pk;
            thisManage.jQe('[c-view-container]').html('<div id="' + id + '"></div>');
            thisManage.viewComp = new thisManage.$options.components[thisManage.viewComponentName]({
                propsData: {
                    cModel: thisManage.modelName,
                    cPk: pk,
                    cConf: thisManage.viewConf,
                    cBig: true,
                }
            });
            thisManage.viewComp.$mount('#' + id);
            thisManage.jQe('[c-view_dialog]').modal('show');
        },
        _createInsert: function (action) {
            var thisManage = this;
            thisManage.updateTitle = 'Inserimento ' + thisManage.translate(thisManage.modelName+'.label');
            var id = 'd' + (new Date().getTime());
            thisManage.jQe('[c-edit-container]').html('<div id="' + id + '"></div>');
            if (thisManage.insertComp)
                thisManage.insertComp.$destroy();
            // if (thisManage.insertConf.inlineTemplate) {
            //     var v = Vue.component(id,{
            //         extends : thisManage.$options.components[thisManage.insertComponentName],
            //         template : jQuery(thisManage.insertConf.inlineTemplate).html()
            //     });
            //
            //
            //     thisManage.insertComp = new v({
            //         propsData: {
            //             cModel: thisManage.modelName,
            //             cConf: thisManage.insertConf
            //         }
            //     });
            // } else {
            thisManage.insertComp = new thisManage.$options.components[thisManage.insertComponentName]({
                propsData: {
                    cModel: thisManage.modelName,
                    cConf: thisManage.insertConf
                }
            });
            //}
            thisManage.insertComp.$mount('#' + id);
            thisManage.jQe('[c-collapse-edit]').collapse('show');
            thisManage.jQe('[c-collapse-list]').collapse('hide');
        },
        _actionSaveBack: function () {
            var thisManage = this;

            return thisManage.merge(thisManage.$crud.conf['action-save'], {
                text: 'Salva e Torna alla lista',
                afterExecute: function () {
                    thisManage.jQe('[c-collapse-edit]').collapse('hide');
                    thisManage.jQe('[c-collapse-list]').collapse('show');
                    this.view.$destroy();
                    thisManage.listComp.reload();
                    thisManage.jQe('[c-edit-container]').html(' ');
                }
            });
        },
        _actionBack: function () {
            var thisManage = this;
            return {
                execute: function () {
                    thisManage.jQe('[c-collapse-edit]').collapse('hide');
                    thisManage.jQe('[c-collapse-list]').collapse('show');
                    this.view.$destroy();
                    thisManage.listComp.reload();
                    thisManage.jQe('[c-edit-container]').html(' ');
                }
            }
        },
        _getListConfiguration: function (conf) {
            var thisManage = this;
            var modelConf = "Model" + thisManage.pascalCase(conf.modelName);
            var originalConf = window[modelConf] ? window[modelConf] : {};
            //console.log('conf.modelName',conf.modelName,modelConf,originalConf);
            var listConf = null;

            if (!thisManage.cInlineEdit && !conf.inlineEdit) {
                listConf = conf.listConf || originalConf.list || {};
                listConf = thisManage.mergeConfView(thisManage.$crud.conf.list, listConf);
                // se sono presente l'action-edit,action-view o action-insert le ridefinisco per la gestione automatica da parte della c-manage
                if (listConf.actions.indexOf('action-edit') >= 0) {
                    var aEdit = listConf.customActions['action-edit'] || {};
                    aEdit.execute = function () {
                        thisManage._createEdit(this);
                    }
                    listConf.customActions['action-edit'] = aEdit;
                }
                if (listConf.actions.indexOf('action-view') >= 0) {
                    var aView = listConf.customActions['action-view'] || {};
                    aView.execute = function () {
                        thisManage._createView(this);
                    }
                    listConf.customActions['action-view'] = aView;
                }

                if (listConf.actions.indexOf('action-insert') >= 0) {
                    var aInsert = listConf.customActions['action-insert'] || {};
                    aInsert.execute = function () {
                        thisManage._createInsert(this);
                    }
                    listConf.customActions['action-insert'] = aInsert;
                }
            }
            conf.listConf = listConf;
            return conf;
            // var d = {
            //     listComponentName: conf.listComponentName || 'v-list',
            //     listComp: null,
            //     listConf: listConf,
            // };
            // return d;
        },
        _getListEditConfiguration: function (conf) {
            var thisManage = this;
            var modelConf = "Model" + thisManage.pascalCase(conf.modelName);
            var originalConf = window[modelConf] ? window[modelConf] : {};
            //console.log('conf.modelName',conf.modelName,modelConf,originalConf);
            var listEditConf = null;

            if (thisManage.cInlineEdit || conf.inlineEdit) {
                listEditConf = conf.listEditConf || originalConf.listEdit || {};
                listEditConf = thisManage.mergeConfView(thisManage.$crud.conf.listEdit, listEditConf);
                console.log('acions list edit ', listEditConf.actions);
                if (listEditConf.actions.indexOf('action-view') >= 0) {
                    listEditConf.customActions['action-view'] = {
                        execute: function () {
                            thisManage._createView(this);
                        }
                    }
                }

                if (listEditConf.actions.indexOf('action-insert') >= 0) {
                    listEditConf.customActions['action-insert'] = {
                        execute: function () {
                            thisManage._createInsert(this);
                        }
                    }
                }
            }
            conf.listEditConf = listEditConf;
            return conf;
            // var d = {
            //     listEditComponentName: conf.listEditComponentName || 'v-list-edit',
            //     listEditComp: null,
            //     listEditConf: listEditConf,
            // };
            // return d;
        },
        _getSearchConfiguration: function (conf) {
            var thisManage = this;
            var modelConf = "Model" + thisManage.pascalCase(conf.modelName);
            var originalConf = window[modelConf] ? window[modelConf] : {};
            var searchConf = conf.searchConf || originalConf.search || {};
            searchConf = thisManage.mergeConfView(thisManage.$crud.conf.search, searchConf);

            var acSearch = searchConf.customActions['action-search'] || {};

            acSearch.execute = function () {
                var that = this;
                var formData = that.view.getViewData();
                var viewParams = thisManage.listComp.route.getParams();
                formData = that.merge(viewParams, formData);
                thisManage.listComp.route.setParams(formData);
                thisManage.listComp.reload();
                return;
            };
            searchConf.customActions['action-search'] = acSearch;
            conf.searchConf = searchConf;
            return conf;
            // var d = {
            //     searchComponentName: conf.searchComponentName || 'v-search',
            //     searchComp: null,
            //     searchConf: searchConf,
            // };
            // return d;
        },
        _getEditConfiguration: function (conf) {
            var thisManage = this;
            var modelConf = "Model" + thisManage.pascalCase(conf.modelName);
            var originalConf = window[modelConf] ? window[modelConf] : {};

            var editConf = conf.editConf || originalConf.edit || {};
            editConf = thisManage.mergeConfView(thisManage.$crud.conf.edit, editConf);
            // prendo eventuali configurazioni locali al modello.
            var _asb = editConf.customActions['action-save-back'] || {};
            //var _as = editConf.customActions['action-save'] || {};
            editConf = thisManage.mergeConfView(editConf, {
                customActions: {
                    'action-save-back': thisManage.merge(_asb,thisManage._actionSaveBack()),
                    'action-back': thisManage._actionBack(),
                    //'action-save' : thisManage.merge(_as,)
                }
            });
            if (editConf.actions.indexOf('action-save-back') < 0)
                editConf.actions.push('action-save-back');
            console.log("EDITCONFACTIONS::: ",editConf.actions);
            conf.editConf = editConf;
            return conf;
            // var d = {
            //     editComponentName: conf.editComponentName || 'v-edit',
            //     editComp: null,
            //     editConf: editConf,
            // };
            // return d;
        },
        _getInsertConfiguration: function (conf) {
            var thisManage = this;
            var modelConf = "Model" + thisManage.pascalCase(conf.modelName);
            var originalConf = window[modelConf] ? window[modelConf] : {};
            var editConf = thisManage.mergeConfView(thisManage.$crud.conf.edit, (originalConf.edit || {}));
            var insertConf = conf.insertConf || originalConf.insert || editConf;
            insertConf = thisManage.mergeConfView(thisManage.$crud.conf.insert, insertConf);
            //insertConf.routeName = 'insert';
            // prendo eventuali configurazioni locali al modello.
            var _asb = insertConf.customActions['action-save-back'] || {};
            insertConf = thisManage.mergeConfView(insertConf, {
                customActions: {
                    'action-save-back': thisManage.merge(_asb,thisManage._actionSaveBack()),
                    'action-back': thisManage._actionBack()
                }
            });
            if (insertConf.actions.indexOf('action-save-back') < 0)
                insertConf.actions.push('action-save-back');
            var actionSaveIndex = insertConf.actions.indexOf('action-save');
            if (actionSaveIndex >= 0) {
                delete insertConf.actions[actionSaveIndex];
            }
            conf.insertConf = insertConf;
            return conf;

            // var d = {
            //     insertComponentName: conf.insertComponentName || 'v-insert',
            //     insertComp: null,
            //     insertConf: insertConf,
            // };
            // return d;
        },
        _getViewConfiguration: function (conf) {
            var thisManage = this;
            var modelConf = "Model" + thisManage.pascalCase(conf.modelName);
            var originalConf = window[modelConf] ? window[modelConf] : {};
            var viewConf = conf.viewConf || originalConf.view || {};
            viewConf = thisManage.mergeConfView(thisManage.$crud.conf.view, viewConf);
            conf.viewConf = viewConf;
            return conf;

            // var d = {
            //     viewComponentName: conf.viewComponentName || 'v-view',
            //     viewComp: null,
            //     viewConf: viewConf //conf.viewConf || viewConf,
            // };
            // return d;
        }
    },
    template: '#c-manage-template'
});
