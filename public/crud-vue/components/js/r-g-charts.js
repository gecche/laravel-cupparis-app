Vue.component('r-g-charts', {
    extends : crud.components.rBase,
    mounted : function() {
        var that = this;
        var _conf = that.cConf || {};
        if (!_conf.operator) {
            jQuery(that.$el).find('[c-operator]').remove();
        }
        var that =this;
        for (var k in _conf.methods) {
            //console.log('r-base implements methods',k);
            that[k] = function () {
                //console.log('call methods ', k );
                _conf.methods[k].apply(that,this.arguments);
            }
        }

        if (_conf.resources && _conf.resources.length) {
            that.beforeLoadResources();
            that.resourcesLoaded = false;
            that.crudApp.loadResources(_conf.resources,function () {
                console.log('resoures loaded callback',that);
                that.resourcesLoaded = true;
                that.afterLoadResources();
            })
        }

    },
    data :  function () {
        return this.defaultData();
    },
    methods : {
        getFieldName: function () {
            var that = this;
            //console.log('GET FIELD NAME',this.cKey);
            if (that.conf.operator) {
                return this.cKey + '[]';
            }
            return this.cKey;
        },
        getOperatorName : function () {
            var that = this;
            return this.cKey + "_operator";
        },
        defaultData : function () {
            var _c = this.cConf || {};
            var d = {
                //fieldName : this.getFieldName(),
                value: _c.value,
                operator : _c.operator,
                //operatorName : this.getOperatorName(),
                resourcesLoaded : true,
                conf : _c,
            }
            //console.log('r-base::defaultData',d);
            return d;
        },
        beforeLoadResources : function () {
            console.log('rBase.beforeLoadResources')
        },
        afterLoadResources : function () {
            console.log('rBase.afterLoadResources');
        },
        getValue : function() {
            return this.value;
        },
        //events
        change : function () {
            var that = this;
            var methods = that.conf.methods || {};
            if (methods.change) {
                methods.change.apply(that);
            }
        },
        updateConf : function (conf) {
            console.log('update conf old',this.conf,'new',conf);
            this.conf = conf;
        },

    },
    // watch : {
    //     resourcesLoaded : {
    //         deep : true,
    //         handler() {
    //             var that = this;
    //             console.log('resouces Loaded',that.resourcesLoaded)
    //             if (that.resourcesLoaded) {
    //                 jQuery(that.$el).find('[c-autocomplete]').mdbAutocomplete({
    //                     data: that.cConf.metadata.domainValues
    //                 });
    //             }
    //         }
    //     }
    // },
    template: '<div>render base</div>'
});
