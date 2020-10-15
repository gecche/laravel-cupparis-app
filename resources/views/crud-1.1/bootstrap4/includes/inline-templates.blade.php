{{--
Inserire qui eventuali template inline da utilizzare nel c-manage dei modelli per edit/view e insert assegnando alla proprietà inlineTemplate
del ModelConf l'id del template da utlizzare
--}}

<script id="v-list-mattonelle" type="text/x-template">
    <div >
        <h4 v-show="viewTitle">@{{viewTitle}}</h4>
        <c-loading v-if="loading" :error-msg="errorMsg"></c-loading>

        <div v-else class="row">
            <div v-for="(row,index) in widgets" class="col-4" style="height:300px">
                <div >
                    <v-action v-for="(action,name) in recordActions[index]" :c-action="action"></v-action>
                </div>
                <template v-for="(widget,key) in row">
                    <div>
                        <v-widget :c-widget="widget"></v-widget>
                    </div>
                </template>
            </div>
        </div>
    </div>
</script>
