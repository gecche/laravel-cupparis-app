
crud.components.cRouter = Vue.component('c-router',{
    props : ['cDefaultCommand','cContentId'],
    extends : crud.components.cComponent,
    mounted : function() {
        var that = this;
        jQuery( window ).on( 'hashchange', function( e ) {
            that.getHash();
        } );
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
            var hash = "";
            if (window.location.hash.indexOf('!') >= 0) {
                hash = window.location.hash.split('!')[1];
            } else
                hash = window.location.hash.substr(1);
            if (!hash && !that.defaultCommand)
                return ;

            if (!hash) {
                // if (window.localStorage.getItem('myHash'))
                //     hash = window.localStorage.getItem('myHash');
                // else
                    hash = that.defaultCommand
            }

            that.doCmd(hash);
            that.lastHash = hash;
            var path = that.getCmdPath(hash);
            eventParams = {
                path : path,
                hash : hash
            }
            that.$crud.instance.$emit('set-path',eventParams);
            //window.location.hash = '';//'#'+Math.random(1000)+"!"+hash;
            //window.localStorage.setItem('myHash',hash);
            that._updateLinks(window.location.hash);
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
                // potrebbe essere un'ancora percio' nessuna eccezione, semplicemente non faccio nulla
                return ;
                //throw 'Componente non trovato ' + componentName;
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
        _updateLinks : function (href) {
            var that = this;
            var newHref = href.substr(1);  // tolgo la #
            if (href.indexOf('!') >= 0 ) {
                newHref = href.split("!")[1];
            }
            newHref = '#'+ Math.floor(Math.random(100000)*100000) + "!" + newHref;
            jQuery('[href="' + href + '"]').attr('href',newHref);
        }
    },
    template : '<span></span>'
})
