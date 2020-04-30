Vue.component('c-test',{
    props : ['cComponent','cModel','cPk','cProviderName','cConf'],
    extends : crud.components.cComponent,
    data : function () {
        var that = this;
        var d = {
            componentName : that.cComponent?that.cComponent:null,
            conf : that.getConf(),
            pk : that.cPk ? that.cPk:null,
            model : that.cModel?that.cModel:null,
            providerName : that.cProviderName?that.cProviderName:null,
        }

        return d;
    },
    methods : {
        getConf : function () {
            var that = this;
            var confName = that.cConf || that.camelCase(this.cComponent+'-conf');
            console.log('c-test confName',confName);
            var confObj = (window[confName]?window[confName]():null) || {};
            confObj.cRef = 'test-component';
            console.log('c-test confObj',confObj);
            jQuery('body').trigger('set-code',[confObj]);
            return confObj;
        },
        getModel : function () {
            return this.model;
        },

        getPk : function () {
            return this.pk
        },

        getProviderName : function () {
            return this.providerName;
        }

    },
    template : '#c-test-template'
})
