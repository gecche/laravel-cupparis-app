var o1 = Vue.component('o1',{
    extends : crud.components.cComponent,
    data : function() {

        var d = {
            var1 : 'variabilie o'
        }
        return
    },
    methods : {
        defaultData : function () {
            return {

            }
        }
    }
});

var o2 = Vue.component('o2',{
    extends : o1,
    data : function() {
        return {
            var2 : 'variabilie o2'
        }
    },
    methods : {
        defaultData : function () {
            return {

            }
        }
    }
});
