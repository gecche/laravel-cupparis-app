var appThemePath = '/smarty3/';

crud.routes['pages'] = {
    url : appThemePath+'pages/{path}',
}

crud.routes['csv-exporta'] = {
    url : 'foormaction/csv-export/{foorm}/{foormtype}',///{foormpk?}',
    method : 'post',
    resultType  : 'record',
}
crud.routes['pdf-exporta'] = {
    url : 'foormaction/pdf-export/{foorm}/{foormtype}',///{foormpk?}',
    method : 'post',
    resultType  : 'record',
}
crud.routes.delete.url = "/foormaction/delete/{modelName}/list";
crud.actions['action-delete'].setRouteValues = function(route) {
    var that = this;
    route.setValues({
        modelName: that.view.modelName
    });
    route.setParams({
        id : that.modelData[that.view.conf.primaryKey]
    });
    return route;
};

crud.routes['multi-delete'].url = "/foormaction/multi-delete/{modelName}/list";
crud.routes['uploadfile'].url = "/foormaction/uploadfile/{modelName}/edit";


crud.routes.set.url = "/foormaction/set/{modelName}/list";

crud.routes['autocomplete'].url = "/foormaction/autocomplete/{foormName}/{viewType}";
crud.routes['autocomplete'].method = 'post';


crud.routes['list-constraint'] = {
    url : '/foormc/{modelName}/{constraintKey}/{constraintValue}',
    resultType : 'list',
    protocol : 'list',
    method : 'get'
};



crud.components.libs = {
    'csv-dashboard' : {
        js : appThemePath+'components/js/csv-dashboard.js',
        tpl : appThemePath+'components/templates/csv-dashboard.html'
    },
    'c-router' : {
        js : appThemePath+'components/js/c-router.js'
    },
    'c-manage': {
        js  : appThemePath+'components/js/c-manage.js',
        tpl : appThemePath+'components/templates/c-manage.html'
    },
    'supplementari' : {
        js  : appThemePath+'custom-components/supplementari.js',
    },
    'c-wizard' : {
        js  : appThemePath+'components/js/c-wizard.js',
        tpl : appThemePath+'components/templates/c-wizard.html'
    },
    'c-drag-drop' : {
        js  : appThemePath+'components/js/c-drag-drop.js',
        tpl : appThemePath+'components/templates/c-drag-drop.html'
    }
}


Vue.component('tpl-search', {
    extends : crud.components.misc.tplBase,
    template : '#tpl-record-in-template'
});

Vue.component('tpl-view', {
    extends : crud.components.misc.tplBase,
    template : '#tpl-view-template'
});


crud.conf.search.widgetTemplate = 'tpl-search';
crud.conf.view.widgetTemplate = 'tpl-view';


crud.conf.edit.beforeActions = null;
crud.conf.edit.beforeForm = null;
crud.conf.insert.beforeActions = null;
crud.conf.insert.beforeForm = null;
crud.conf.list.headerClass = null;
crud.conf.search.buttonsClass = null;
crud.conf.view.defaultWidgetType = 'w-input-view';

console.log('APPLICATION CONFIG LOADED');

crud.actions['action-export-csv'] = {
    execute : function () {
        var that = this;
        var r = that.createRoute(that.routeName);
        r.setValues({
            'foorm' : that.view.modelName,
            'foormtype' : that.view.cType
        });
        var p = {
            'csvType' : that.csvType
        };
        var viewP = that.view.route.getParams();
        r.setParams(that.merge(p,viewP));
        that.waitStart(that.startMessage);
        Server.route(r,function (json) {
            that.waitEnd();
            if (json.error) {
                that.errorDialog(json.msg);
                return ;
            }
            document.location.href = json.result.link;
            console.log(json);
        })

        console.log('r',r);
    },
    type : 'collection',
    icon : "fa fa-file-text-o",
    text : "Descrizioni",
    css : 'btn-sm btn btn-outline-secondary',
    csvType : 'default',
    routeName : 'csv-exporta',
    startMessage : 'Generazione csv in corso...',
};

crud.actions['action-search'].css = "btn-sm mr-1 btn-success bg-success-soft"
crud.actions['action-reset'].css = "btn-sm mr-1 btn-warning bg-warning-soft"
crud.actions['action-save'].css = "btn-sm mr-1 btn-success bg-success-soft";
//crud.actions['action-save'].alertTime = 0;
//crud.actions['action-save-row'].alertTime = 0;
crud.actions['action-back'].css = "btn-sm mr-1 btn-danger bg-danger-soft"

crud.actions['action-export-csv-codici'] = crud.instance.merge(
    crud.actions['action-export-csv'],
    {
        text : "Codici",
        css : 'btn-sm btn btn-outline-primary',
        csvType : 'codici',
    }
);
crud.actions['action-export-csv-standard'] = crud.instance.merge(
    crud.actions['action-export-csv'],
    {
        text : "Export",
    }
);
crud.actions['action-export-csv-riepilogo'] = crud.instance.merge(
    crud.actions['action-export-csv'],
    {
        text : "Export riepilogo",
        csvType : 'riepilogo',
    }
);

crud.actions['action-save-back'] = crud.instance.merge(
    crud.actions['action-save'], {
        text : 'Salva e torna alla lista',
        css : "btn-sm mr-1 btn-success bg-success-soft",
        //alertTime: 0,

});

crud.actions['action-insert'].execute = function () {
    var that = this;
    var id = that.modelData[that.view.primaryKey];
    document.location.href = '#v-insert?cModel='+that.view.modelName;
}

crud.actions['action-delete'].execute = function () {
        var that = this;
        var confirmMessage;
        if (that.view.modelName == 'istituto') {
            confirmMessage = 'Attenzione! Cancellando l\'istituto verranno cancellati DEFINITIVAMENTE tutti i progetti ' +
                'associati, le informazioni di contatto, i referenti e lo storico dei contatti.<br/>' +
                'Sicuro di voler continuare?';
        } else {
            confirmMessage = that.$crud.lang['app.conferma-cancellazione'];
        }
        that.confirmDialog(confirmMessage ,{
            ok : function () {
                var r = that.createRoute('delete');
                that.setRouteValues(r);
                Server.route(r,function (json) {
                    if (json.error) {
                        that.errorDialog(json.msg);
                        return ;
                    }
                    var msg = json.msg?json.msg:that.translate('app.cancellazione-successo');
                    that.alertSuccess(msg);
                    that.view.reload();
                });
            }
        });
}

