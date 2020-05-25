
crud.components.cRouter = Vue.component('c-router',{
    props : ['cDefaultCommand','cContentId'],
    extends : crud.components.cComponent,
    mounted : function() {
        var that = this;
        // var _f = function() {
        //     var hash = window.location.hash.substr(1);
        //     if (!hash && !that.defaultCommand)
        //         return ;
        //     if (!hash)
        //         hash = that.defaultCommand
        //     that.doCmd(hash);
        //     that.lastHash = hash;
        //     var path = that.getCmdPath(hash);
        //     // var path = [
        //     //     { label : 'Dashboard'}
        //     // ];
        //     // console.log('emit event set-path ',path);
        //     eventParams = {
        //         path : path,
        //         hash : hash
        //     }
        //     that.$crud.instance.$emit('set-path',eventParams);
        //     //app.$emit('set-path',path);
        // }
        jQuery( window ).on( 'hashchange', function( e ) {
            //_f()
            that.getHash();
        } );
        //_f();
        that.getHash();
    },
    data : function() {
        return {
            lastComponent : null,
            defaultCommand : this.cDefaultCommand?this.cDefaultCommand:null,
            contentId : this.cContentId?this.cContentId:'app-content',
            lastHash : null
        }
    },
    methods : {

        getHash : function() {
            var that = this;
            var hash = window.location.hash.substr(1);
            if (!hash && !that.defaultCommand)
                return ;
            if (!hash)
                hash = that.defaultCommand
            that.doCmd(hash);
            that.lastHash = hash;
            var path = that.getCmdPath(hash);
            // var path = [
            //     { label : 'Dashboard'}
            // ];
            // console.log('emit event set-path ',path);
            eventParams = {
                path : path,
                hash : hash
            }
            that.$crud.instance.$emit('set-path',eventParams);
            window.location.hash = '';
        },
        getCmdPath : function(cmd) {
            if (!cmd)
                return
            var tmp = cmd.split('?');
            console.log('split command',tmp);
            return [{label : tmp[0] }];
        },
        go : function(target) {
            var that = this;
            var href = jQuery(target.target).closest('a').attr('href');
            if (href) {
                href = href.substr(1);
                that.doCmd(href);
            }
        },

        doCmd : function (command) {
            var that = this;
            console.log('COMMAND ',command);

            var tmp = command.split('?');
            if (tmp[0] == 'page') {
                if (that.lastComponent)
                    that.lastComponent.$destroy();

                var params = that.getAllUrlParams(command);

                var route = that.createRoute('pages');
                route.setValues({
                    path : params['path']
                })
                delete params['path'];
                route.setParams(params);
                Server.route(route,function (html) {

                    var cdef = Vue.component('async-comp', {
                            extends : crud.components.cComponent,
                            template : html
                    });

                    var id= 'd' + (new Date().getTime());
                    jQuery(that.contentId).html('<div id="'+id+'" ></div>');
                    var componente = new cdef();
                    componente.$mount('#'+id);
                    that.lastComponent = componente;
                    //jQuery(that.contentId).html(html);
                })
                return ;
            }

            var componentName = tmp[0];
            var params = that.getAllUrlParams(command);
            console.log('componente',componentName,'params',params);

            console.log('that',that);
            if (!that.$options.components[componentName]) {
                throw 'Componente non trovato ' + componentName;
            }

            if (that.lastComponent)
                that.lastComponent.$destroy();

            var componente = new that.$options.components[componentName]({
                propsData : params,
                ref : componentName
            });
            var id= 'd' + (new Date().getTime());
            jQuery(that.contentId).html('<div id="'+id+'" ></div>');
            componente.$mount('#'+id);
            that.lastComponent = componente;

            return;





        },
        // pushHash : function (hashString) {
        //     var hash = '#'+hashString
        //     window.location.replace(hash);
        //     history.replaceState(undefined, undefined,hash);
        //     return ;
        //     if(window.history.pushState) {
        //         console.log('pushState')
        //         window.history.pushState(null, null, hash);
        //     }
        //     else {
        //         window.location.hash = hash;
        //     }
        // }
    },
    template : '<span></span>'
})
