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
crud.conf['action-delete'].setRouteValues = function(route) {
    var that = this;
    route.setValues({
        modelName: that.view.modelName
    });
    route.setParams({
        id : that.modelData[that.view.primaryKey]
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

crud.routes['edit-constraint'] = {
    url : '/foormc/{modelName}/{constraintKey}/{constraintValue}/{pk}/edit',
    resultType : 'record',
    protocol : 'record',
    method : 'get'
};

crud.routes['insert-constraint'] = {
    url : '/foormc/{modelName}/{constraintKey}/{constraintValue}/new',
    resultType : 'record',
    protocol : 'record',
    method : 'get'
};

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

crud.conf['action-export-csv'] = {
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

crud.conf['action-search'].css = "btn-sm mr-1 btn-success bg-success-soft"
crud.conf['action-reset'].css = "btn-sm mr-1 btn-warning bg-warning-soft"
crud.conf['action-save'].css = "btn-sm mr-1 btn-success bg-success-soft";
//crud.conf['action-save'].alertTime = 0;
//crud.conf['action-save-row'].alertTime = 0;
crud.conf['action-back'].css = "btn-sm mr-1 btn-danger bg-danger-soft"

crud.conf['action-export-csv-codici'] = crud.instance.merge(
    crud.conf['action-export-csv'],
    {
        text : "Codici",
        css : 'btn-sm btn btn-outline-primary',
        csvType : 'codici',
    }
);
crud.conf['action-export-csv-standard'] = crud.instance.merge(
    crud.conf['action-export-csv'],
    {
        text : "Export",
    }
);
crud.conf['action-export-csv-riepilogo'] = crud.instance.merge(
    crud.conf['action-export-csv'],
    {
        text : "Export riepilogo",
        csvType : 'riepilogo',
    }
);

crud.conf['action-save-back'] = crud.instance.merge(
    crud.conf['action-save'], {
        text : 'Salva e torna alla lista',
        css : "btn-sm mr-1 btn-success bg-success-soft",
        //alertTime: 0,

});

crud.conf['action-insert'].execute = function () {
    var that = this;
    var id = that.modelData[that.view.primaryKey];
    document.location.href = '#v-insert?cModel='+that.view.modelName;
}

crud.conf['action-previous'] = {
    text : '<<',
    title : 'Precedente',
    execute : function () {
        this.view._backward();
    }
}
crud.conf['action-next'] = {
    text : '>>',
    title : 'Successivo',
    execute : function () {
        this.view._forward();
    }
}
crud.routes['wizard'] = {
    url : '/test-passo/{passo}',
    method : 'get',
    type : 'record',
    protocol : 'record',
}
