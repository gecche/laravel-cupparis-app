var ModelUser = {
    search : {
        modelName : 'user',
        //langContext : 'user',
        fields : ['email','name','roles|id'],
        fieldsConfig : {
            'email' : {
                operator : 'like',

            },
            'roles|id' : {
                type: 'w-select'

            }
        }
    },
    view : {
        modelName : 'user',
        //fields : ['name','email','password','password_confirmation','banned','mainrole','fotos','attachments'],
        actions : [],
        fieldsConfig : {
            mainrole : {
                type : 'w-belongsto',
                labelFields : ['name']
            }
        }
    },
    list : {
        modelName : 'user',
        fields : ['id','email','name',
            // 'email_verified_at',
            'banned','mainrole'],
        actions : ['action-edit','action-delete','action-insert','action-delete-selected'],
        //actions : ['action-delete-selected'],
        orderFields : {
            'email':'email'
        },
        fieldsConfig : {
            email_verified_at : {
                type : 'w-swap',
                modelName : 'user',
            },
            banned : {
                type : 'w-swap-smarty',
                modelName : 'user',
                switchClass: 'form-switch-danger banned',
                dataSwitched : true,
            },
            fotos : {
                type : 'w-hasmany-view',
                hasmanyConf : {
                    fields : ['resource'],
                    fieldsConfig : {
                        resource : {
                            template : 'tpl-no',
                            type : 'w-preview'
                        }
                    }
                }
            },
            attachments : {
                type : 'w-hasmany-view',
                hasmanyConf : {
                    fields : ['resource'],
                    fieldsConfig : {
                        resource : {
                            type : 'w-preview',
                            template : 'tpl-no',
                        }
                    }
                }
            }
        }
    },
    edit : {
        modelName : 'user',
        actions : ['action-save','action-back','action-test'],
        fields : ['name','email','password','password_confirmation','mainrole',
            // 'fotos','attachments'
        ],
        methods : {
            fillData : function (route,json) {
                var that = this;
                if (json.metadata.is_auth) {
                    that.fieldsConfig.mainrole = 'w-hidden';
                }
                that.$options.methods.fillData.apply(that,[route,json]);
            }
        },
        fieldsConfig: {
            mainrole : 'w-select',
            banned : 'w-radio',
            password : {
                type:'w-input',
                inputType:'password',
            },
            password_confirmation : {
                type:'w-input',
                inputType:'password',
            },
            //roles : 'w-select',
            attachments : {
                type : 'w-hasmany',
                hasmanyConf : {
                    fields : ['id','nome','descrizione','resource','status'],
                    fieldsConfig : {
                        resource : {
                            type : 'w-upload-ajax',
                            extensions : ['csv','xls'],
                            maxFileSize : '2M',
                            ajaxFields : {
                                field : 'attachments|resource',
                                resource_type : 'attachment'
                            },
                            modelName : 'user'
                        },
                        status : 'w-hidden',
                        id : 'w-hidden',
                    },
                },
            },
            fotos : {
                type : 'w-hasmany',
                hasmanyConf : {
                    fields : ['status','id','nome','descrizione','resource'],
                    fieldsConfig : {
                        resource : {
                            type : 'w-upload-ajax',
                            extensions : ['jpg','png'],
                            maxFileSize : '2M',
                            ajaxFields : {
                                field : 'fotos|resource'
                            },
                            modelName : 'user'
                        },
                        status : 'w-hidden',
                        id : 'w-hidden'

                    },
                }
            }
        },
        customActions : {
            'action-test' : {
                visible : function () {
                    return false;
                },
                //enabled : false,
                enabled : function() {
                    return false;
                },
                text : 'test'
            }
        }
    },
    // view : {
    //     //actions: ['action-save', 'action-back', 'action-test'],
    //     fields: ['name','email', 'password', 'password_confirmation', 'banned', 'mainrole', 'fotos', 'attachments'],
    //     fieldsConfig :  {
    //         mainrole :  {
    //             type : 'w-belongsto',
    //             fields : ['name']
    //         }
    //     }
    // },
    'listEdit' : {
        fieldsConfigEditMode : {
            'id' : 'w-text',
            'email' : 'w-text',
            'banned' : 'w-text'
        }
    }
}
