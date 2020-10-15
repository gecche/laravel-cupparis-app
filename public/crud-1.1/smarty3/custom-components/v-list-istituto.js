crud.components.views.vListIstituto = Vue.component('v-list-istituto',{
    name : 'v-list-istituto',
    extends : crud.components.views.vList,
    template : '#v-list-istituto-template',
    data : function() {
        return {
            hasReferentirecenti : 0
        }
    },
    methods :  {
        toggleReferenti : function (event) {
            console.log(event);
            var params = this.route.getParams();
            var vSearch = this.$crud.cRefs['v-search-istituto'];
            if (event.target.checked) {
                params.s_referentirecenti = 1;
                this.fieldsConfig.referentirecenti.type = 'w-custom';
                vSearch.fieldsConfig['referentirecenti|T_REFERENTE_COGNOME'].type = 'w-input';
                vSearch.fieldsConfig['referentirecenti|T_REFERENTE_NOME'].type = 'w-input';
            } else {
                params.s_referentirecenti = '';
                this.fieldsConfig.referentirecenti.type = 'w-hidden';
                vSearch.fieldsConfig['referentirecenti|T_REFERENTE_COGNOME'].type = 'w-hidden';
                vSearch.fieldsConfig['referentirecenti|T_REFERENTE_NOME'].type = 'w-hidden';
                delete params['s_referentirecenti|T_REFERENTE_COGNOME'] ;
                delete params['s_referentirecenti|T_REFERENTE_NOME'];
            }
            vSearch.loading = true;
            vSearch.draw();
            this.hasReferentirecenti = params.s_referentirecenti;
            this.route.setParams(params);
            this.reload();
        }
    }
})
