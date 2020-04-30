
crud.routes.delete.url = "/foormaction/delete/{modelName}/list";
crud.recordActions['action-delete'].setRouteValues = function(route) {
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
crud.components.widgets.wSwap2 = Vue.component('w-swap2',{
    extends : crud.components.widgets.wSwap,
    methods : {
        setRouteValues : function(route) {
            var that = this;
            console.log('rswap RIDEFINITO')
            var dV = that.getDV();
            var keys = Object.keys(dV);
            var value = that.value?that.value:keys[0];
            var vs = keys.map(String);
            var index = vs.indexOf(""+value);
            index = (index + 1) % vs.length;
            console.log('rswap',that);
            route.setValues({
                modelName: that.conf.model,
                //field : that.name, //that.conf.key?that.conf.key:that.cKey,
                //value : keys[index]
            });
            route.setParams({
                id:that.conf.modelData.id,
                field : that.name,
                value : keys[index]
            });
            return route;
        }
    }
})

crud.routes['autocomplete'].url = "/foormaction/autocomplete/{modelName}/edit";
crud.routes['autocomplete'].method = 'post';
crud.components.widgets.wAutocomplete2 = Vue.component('w-autocomplete2',{
    extends : crud.components.widgets.wAutocomplete,
    methods : {
        setRouteValues : function(route,term) {
            var that = this;
            //var r = that.$crud.createRoute(that.conf.routeName);
            route.setValues({modelName:that.modelName});
            //var r = new Route(routeConf);

            //var url = that.url?that.url:"/api/json/autocomplete/" + that.metadata.autocompleteModel + "?";
            var url = that.url?that.url:route.getUrl();
            url+= '?value='+term+'&';
            route.setParams({
                field : that.name
            })

            // if (that.conf.fields) {
            //     for(var f in that.conf.fields) {
            //         url+="field[]="+that.conf.fields[f]+"&";
            //     }
            // }
            /* @TODO se metto la description diventa difficile cambiare la
             if (that.model_description) {
             for(var f in that.model_description) {
             url+="description[]="+that.model_description[f]+"&";
             }
             }
             */
            url += that.conf.separator ? '&separator=' + that.conf.separator : '';
            url += that.conf.n_items ? '&n_items=' + that.conf.n_items : '';
            url += that.conf.method ? '&method=' + that.conf.method: '';
            route.setUrl(url);
            return route;
            //return url;
        }
    }
})


crud.components.libs = {
    'csv-dashboard' : {
        js : '/bootstrap4/components/js/csv-dashboard.js',
        tpl : '/bootstrap4/components/templates/csv-dashboard.html'
    },
    'c-menu' : {
        js : '/bootstrap4/components/js/c-router.js'
    },
    'c-manage': {
        js  : '/bootstrap4/components/js/c-manage.js',
        tpl : '/bootstrap4/components/templates/c-manage.html'
    },
    'c-test' : {
        js  : '/bootstrap4/components/js/c-test.js',
        tpl : '/bootstrap4/components/templates/c-test.html'
    }
}

console.log('APPLICATION CONFIG LOADED');
