//-----------------   WIDGETS ---------------------
crud.components.widgets.wAutocomplete = Vue.component('w-autocomplete', {
    extends : crud.components.widgets.coreWAutocomplete ,
    template: "#w-autocomplete-template",
    methods : {
        setRouteValues : function(route,term) {
            var that = this;
            route.setValues({modelName:that.modelName});
            var url = that.url?that.url:route.getUrl();
            //url+= '?value='+term+'&';
            route.setParams({
                field : that.name,
                value : term,
            })
            url += that.separator ? '&separator=' + that.separator : '';
            url += that.n_items ? '&n_items=' + that.n_items : '';
            url += that.method ? '&method=' + that.method: '';
            route.setUrl(url);
            return route;
        }
    }
});

crud.components.widgets.wB2Select2 = Vue.component('w-b2-select2', {
    extends : crud.components.widgets.coreWB2Select2 ,
    template: '#w-b2-select2-template',
});

crud.components.widgets.wB2mSelect2 = Vue.component('w-b2m-select2', {
    extends : crud.components.widgets.coreWB2mSelect2 ,
    template: '#w-b2m-select2-template',
});

crud.components.widgets.wBelongsto = Vue.component('w-belongsto', {
    extends : crud.components.widgets.coreWBelongsto,
    template: '#w-belongsto-template',
});

crud.components.widgets.wCheckbox = Vue.component('w-checkbox',{
    extends : crud.components.widgets.coreWCheckbox,
    template: '#w-checkbox-template',
});

crud.components.widgets.wCustom = Vue.component('w-custom', {
    extends : crud.components.widgets.coreWCustom,
    template: '#w-custom-template',
});

crud.components.widgets.wDatePicker = Vue.component('w-date-picker', {
    extends: crud.components.widgets.coreWDatePicker,
    template: '#w-date-picker-template',
});

crud.components.widgets.wDateSelect = Vue.component('w-date-select', {
    extends: crud.components.widgets.coreWDateSelect,
    template: '#w-date-select-template',
});

crud.components.widgets.wDownload = Vue.component('w-download', {
    extends: crud.components.widgets.coreWDownload,
    template: '#w-download-template',
});

crud.components.widgets.wHasmany =Vue.component('w-hasmany', {
    extends: crud.components.widgets.coreWHasmany,
    template: '#w-hasmany-template',
});

crud.components.widgets.wHasmanyThrough =Vue.component('w-hasmany-through', {
    extends: crud.components.widgets.coreWHasmanyThrough,
    template: '#w-hasmany-through-template',
});

crud.components.widgets.wHasmanyView = Vue.component('w-hasmany-view', {
    extends : crud.components.widgets.coreWHasmanyView,
    template: '#w-hasmany-view-template',
});

crud.components.widgets.wHidden = Vue.component('w-hidden', {
    extends : crud.components.widgets.coreWHidden,
    template: '#w-hidden-template'
});

crud.components.widgets.wImage = Vue.component('w-image',{
    extends : crud.components.widgets.coreWImage,
    template: '#w-image-template'
});

crud.components.widgets.wInput = Vue.component('w-input', {
    extends : crud.components.widgets.coreWInput,
    template: '#w-input-template',
});

crud.components.widgets.wInputHelped =  Vue.component('w-input-helped', {
    extends: crud.components.widgets.coreWInputHelped,
    template: '#w-input-helped-template',
});

crud.components.widgets.wPreview = Vue.component('w-preview', {
    extends: crud.components.widgets.coreWPreview,
    template: '#w-preview-template',
});

crud.components.widgets.wRadio = Vue.component('w-radio', {
    extends: crud.components.widgets.coreWRadio,
    template: '#w-radio-template',
});

crud.components.widgets.wSelect = Vue.component('w-select', {
    extends: crud.components.widgets.coreWSelect,
    template: '#w-select-template',
});

crud.components.widgets.wSwap = Vue.component('w-swap', {
    extends: crud.components.widgets.coreWSwap,
    template: '#w-swap-template',
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
                modelName: that.modelName,
            });
            route.setParams({
                id:that.conf.modelData.id,
                field : that.name,
                value : keys[index]
            });
            return route;
        }
    }
});

crud.components.widgets.wStatus = Vue.component('w-status', {
    extends: crud.components.widgets.coreWStatus,
    template: '#w-status-template',
});

crud.components.widgets.wText = Vue.component('w-text',{
    extends : crud.components.widgets.coreWText,
    template: '#w-text-template'
});

crud.components.widgets.wTextarea = Vue.component('w-textarea', {
    extends : crud.components.widgets.coreWTextarea,
    template: '#w-textarea-template'
});

crud.components.widgets.wTexthtml = Vue.component('w-texthtml', {
    extends: crud.components.widgets.coreWTexthtml,
    template: '#w-texthtml-template',
});

crud.components.widgets.wUpload = Vue.component('w-upload', {
    extends: crud.components.widgets.coreWUpload,
    template: '#w-upload-template',
});

crud.components.widgets.wUploadAjax = Vue.component('w-upload-ajax', {
    extends: crud.components.widgets.coreWUploadAjax,
    template: '#w-upload-ajax-template',
});

//-----------------   VIEWS  ----------------------

crud.components.views.vEdit = Vue.component('v-edit', {
    extends : crud.components.views.coreVEdit,
    template : '#v-edit-template'
});

crud.components.views.vHasmany = Vue.component('v-hasmany', {
    extends: crud.components.views.coreVHasmany,
    template: '#v-hasmany-template',
});

crud.components.views.vHasmanyView = Vue.component('v-hasmany-view', {
    extends: crud.components.views.coreVHasmanyView,
    template: '#v-hasmany-template',
});

crud.components.views.vInsert = Vue.component('v-insert', {
    extends: crud.components.views.coreVInsert,
    template: '#v-insert-template',
});

crud.components.views.vList = Vue.component('v-list', {
    extends: crud.components.views.coreVList,
    template: '#v-list-template',
});

crud.components.views.vListEdit = Vue.component('v-list-edit', {
    extends: crud.components.views.coreVListEdit,
    template: '#v-list-edit-template',
});

crud.components.views.vSearch = Vue.component('v-search', {
    extends: crud.components.views.coreVSearch,
    template: '#v-search-template',
});

crud.components.views.vView = Vue.component('v-view', {
    extends: crud.components.views.coreVView,
    template: '#v-view-template',
});
//-----------------   ACTIONS ---------------------

crud.components.actions.actionBase = Vue.component('action-base', {
    extends : crud.components.actions.coreActionBase,
    template: '#action-template'
});

Vue.component('action-edit', {
    extends : crud.components.actions.actionBase
});

Vue.component('action-view', {
    extends : crud.components.actions.actionBase
});

Vue.component('action-save', {
    extends : crud.components.actions.actionBase
});

Vue.component('action-insert', {
    extends : crud.components.actions.actionBase
});

Vue.component('action-back', {
    extends : crud.components.actions.actionBase
});

Vue.component('action-search', {
    extends : crud.components.actions.actionBase
});

Vue.component('action-reset', {
    extends : crud.components.actions.actionBase
});

Vue.component('action-delete', {
    extends : crud.components.actions.actionBase
});

Vue.component('action-delete-selected', {
    extends : crud.components.actions.actionBase
});

Vue.component('action-edit-mode',{
    extends : crud.components.actions.actionBase
});

Vue.component('action-view-mode',{
    extends : crud.components.actions.actionBase
});

Vue.component('action-save-row',{
    extends : crud.components.actions.actionBase
});

crud.components.actions.actionOrder = Vue.component('action-order',{
    extends : crud.components.actions.coreActionOrder,
    template: '#action-order-template'
});


//-----------------   MISCELLANEOUS ---------------------

crud.components.misc.cLoading = Vue.component('c-loading',{
    extends : crud.components.misc.coreCLoading,
    template : '#c-loading-template',
});

crud.components.misc.cPaginator  = Vue.component('c-paginator',{
    extends : crud.components.misc.coreCPaginator,
    template : '#c-paginator-template',
});

crud.components.misc.cWait = Vue.component('c-wait',{
    extends : crud.components.misc.coreCWait,
    template: '#c-wait-template',
});



Vue.component('tpl-record',{
    extends : crud.components.misc.tplBase,
    template : '#tpl-record-template'
});

Vue.component('tpl-record2',{
    extends : crud.components.misc.tplBase,
    template : '#tpl-record2-template'
});

Vue.component('tpl-list', {
    extends : crud.components.misc.tplBase,
    template : '#tpl-list-template'
});

Vue.component('tpl-no', {
    extends : crud.components.misc.tplBase,
    template : '#tpl-no-template'
});


crud.components.misc.dConfirm = Vue.component('d-confirm', {
    extends : crud.components.misc.coreDConfirm,
    template : '#d-confirm-template'
});

crud.components.misc.dMessage = Vue.component('d-message', {
    extends : crud.components.misc.coreDMessage,
    template : '#d-message-template'
});

crud.components.misc.dError = Vue.component('d-error', {
    extends : crud.components.misc.coreDError,
    template : '#d-error-template'
});
crud.components.misc.dWarning = Vue.component('d-warning', {
    extends : crud.components.misc.coreDWarning,
    template : '#d-warning-template'
});

crud.components.misc.dCustom = Vue.component('d-custom', {
    extends : crud.components.misc.coreDCustom,
    template : '#d-custom-template'
});
