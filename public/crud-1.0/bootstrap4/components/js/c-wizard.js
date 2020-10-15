crud.components.cWizard = Vue.component('c-wizard', {
    extends: crud.components.views.vInsert,
    template: '#c-wizard-template',
    mounted : function() {
        this._wizardInit();
    },
    data : function() {
        var that = this;
        var _conf = that._getConf() || {};
        return {
            currentStep : 0,
            maxStep : _conf.maxStep || 0,
        }
    },
    computed :  {

    },
    methods : {
        setRouteValues : function (route) {
            var that  = this;
            if (route) {
                route.setValues({
                    passo : that.currentStep
                });
            }
            return route;
        },
        _wizardInit : function() {
            var that = this;
            if (!that.wizardInit)
                return ;
            that.fields = that.fieldsStep[0];
            that.fieldsConfig = that.fieldsStepConfig[0];
            if (!jQuery.isFunction(that.wizardInit)) {
                throw "'wizardInit' deve essere una funzione";
            }
            that.wizardInit.apply(that,[]);
        },
        _forward: function () {
            var that = this;
            if (that.currentStep >=  that.maxStep-1)
                return ;

            if (!jQuery.isFunction(that.forward)) {
                throw "'forward' deve essere una funzione";
            }
            var __callback = function (validateResult) {
                if (validateResult) {
                    that.currentStep++;
                    console.log('forward currentStep',that.currentStep);
                    that.forward.apply(that,[]);
                }
            }
            that._validate.apply(that,['forward',__callback]);
        },
        _backward : function () {
            var that = this;
            if (that.currentStep == 0)
                return ;
            if (!jQuery.isFunction(that.backward)) {
                throw "'backward' deve essere una funzione";
            }
            var __callback = function (validateResult) {
                if (validateResult) {
                    that.currentStep--;
                    console.log('backward currentStep',that.currentStep)
                    that.backward.apply(that,[]);
                }
            }
            that._validate.apply(that,['backward',__callback]);
        },
        _validate : function (action,callback) {
            var that = this;
            console.log('validate currentStep',that.currentStep)
            if (!that.validate)
                return true;
            if (!jQuery.isFunction(that.validate)) {
                throw "'validate' deve essere una funzione";
            }
            return that.validate.apply(that,[action,callback]);
        }
    }
})
