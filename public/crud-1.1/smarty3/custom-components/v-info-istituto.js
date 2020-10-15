Vue.component('v-info-istituto',{
    name : 'v-info-istituto',
    extends : crud.components.views.vView,
    template : '#v-info-istituto-template',
    data : function() {
        return {
            hasReferenti : 0
        }
    },
    methods :  {
        toggleReferenti : function (event) {
            console.log(event);
            var params = this.route.getParams();
            var vSearch = this.$crud.cRefs['v-search-istituto'];
            if (event.target.checked) {
                params.s_referenti = 1;
                this.fieldsConfig.referenti.type = 'w-custom';
                vSearch.fieldsConfig['referenti|T_REFERENTE_COGNOME'].type = 'w-input';
                vSearch.fieldsConfig['referenti|T_REFERENTE_NOME'].type = 'w-input';
            } else {
                params.s_referenti = '';
                this.fieldsConfig.referenti.type = 'w-hidden';
                vSearch.fieldsConfig['referenti|T_REFERENTE_COGNOME'].type = 'w-hidden';
                vSearch.fieldsConfig['referenti|T_REFERENTE_NOME'].type = 'w-hidden';
                delete params['s_referenti|T_REFERENTE_COGNOME'] ;
                delete params['s_referenti|T_REFERENTE_COGNOME'];
            }
            vSearch.loading = true;
            vSearch.draw();
            this.hasReferenti = params.s_referenti;
            this.route.setParams(params);
            this.reload();
        }
    }
})
