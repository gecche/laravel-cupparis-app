
crud.routes.delete.url = "/foormaction/delete/{modelName}/list";
crud.conf['action-mia'] = {
    text : 'action mia',
    execute : function () {
        alert('mia '+ this.view.getWidget('email').getValue());
    }
}

crud.conf['action-delete'].setRouteValues = function(route) {
    var that = this;
    route.setValues({
        modelName: that.view.cModel
    });
    route.setParams({
        id : that.modelData[that.view.conf.primaryKey]
    });
    return route;
};

crud.routes['multi-delete'].url = "/foormaction/multi-delete/{modelName}/list";
crud.routes['uploadfile'].url = "/foormaction/uploadfile/{modelName}/edit";


crud.routes.set.url = "/foormaction/set/{modelName}/list";
crud.routes['autocomplete'].url = "/foormaction/autocomplete/{modelName}/edit";
crud.routes['autocomplete'].method = 'post';

crud.components.libs = {
    'csv-dashboard' : {
        js : '/bootstrap4/components/js/csv-dashboard.js',
        tpl : '/bootstrap4/components/templates/csv-dashboard.html'
    },
    'c-router' : {
        js : '/bootstrap4/components/js/c-router.js'
    },
    'c-manage': {
        js  : '/bootstrap4/components/js/c-manage.js',
        tpl : '/bootstrap4/components/templates/c-manage.html'
    },
    'c-wizard': {
        js  : '/bootstrap4/components/js/c-wizard.js',
        tpl : '/bootstrap4/components/templates/c-wizard.html'
    },
    'c-drag-drop': {
        js  : '/bootstrap4/components/js/c-drag-drop.js',
        tpl : '/bootstrap4/components/templates/c-drag-drop.html'
    },

    // 'c-test' : {
    //     js  : '/bootstrap4/components/js/c-test.js',
    //     tpl : '/bootstrap4/components/templates/c-test.html'
    // }
}

console.log('APPLICATION CONFIG LOADED');

crud.conf['action-insert'].execute = function () {
    var that = this;
    var id = that.modelData[that.view.primaryKey];
    document.location.href = '#v-insert?cModel='+that.view.modelName;
}


crud.conf['action-edit'].execute = function () {
    var that = this;
    var id = that.modelData[that.view.primaryKey];
    document.location.href = '#v-edit?cModel='+that.view.modelName+'&cPk='+id;
}

crud.conf['action-view'].execute = function () {
    var that = this;
    var id = that.modelData[that.view.primaryKey];
    var view = that.createModalView('v-view',{
        cModel : that.view.modelName,
        cPk : id
    })

    //document.location.href = '#v-edit?cModel='+that.view.modelName+'&cPk='+id;
}

crud.conf['action-search'].execute = function () {
    var that = this;
    var params = that.view.getViewData();
    console.log('params',params);
    crud.instance.viewRouteConf = {
        params : params
    };
    var s =  encodeURI(crud.instance.viewRouteConf.toString());
    document.location.href = '#v-list?cModel='+that.view.modelName+'&cRouteConf='+s;
    //document.location.href = '#v-list?cModel='+that.view.modelName+'&cRouteConf='+'instance.viewRouteConf';
    // var id = that.modelData[that.view.primaryKey];
    // document.location.href = '#v-edit?cModel='+that.view.modelName+'&cPk='+id;
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
