crud.components.views.vListConstraint = Vue.component('v-list-constraint', {
    extends: crud.components.views.vList,
    template: '#v-list-template',
    data : function() {
        return {
            routeName: 'list-constraint'
        }
    },
    methods : {
        setRouteValues : function (route) {
            var that = this;
            route.setValues({
                modelName : that.modelName,
                constraintKey : that.constraintKey,
                constraintValue : that.constraintValue,
            })
        }
    }
})
