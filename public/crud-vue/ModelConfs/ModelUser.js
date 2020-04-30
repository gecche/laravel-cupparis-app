var ModelUser = {
    search : {
        modelName : 'user',
        fields : ['email','name'],
        fieldsConfig : {
            'email' : {
                operator : 'like',

            }
        }
    },
    view : {
        modelName : 'user'
    },
    list : {
        modelName : 'user',
        //fields : ['email','name','surname'],
        actions : ['action-edit','action-delete','action-insert','action-delete-selected'],
        orderFields : {
            'email':'email'
        },
        fieldsConfig : {
            verified : {
                type : 'r-swap',
                metadata : {
                    modelName : 'user'
                }
            },
            activated : {
                type : 'r-swap',
                metadata : {
                    modelName : 'user'
                }
            },
            // fotos : {
            //     type : 'r-hasmany-image'
            // }
        }
    },
    edit : {
        actions : ['action-save','action-back','action-test'],
        //fields : ['fotos'],
        fieldsConfig: {
            activated : 'r-radio',
            attachments : {
                type : 'r-hasmany',
                hasmanyConf : {
                    fields : ['id','nome','descrizione','resource_id','status','url'],
                    fieldsConfig : {
                        resource_id : {
                            type : 'r-upload-ajax',
                            metadata : {
                                extensions : ['csv','xls'],
                                maxFileSize : '2M',
                                ajaxFields : {
                                    resource_type : 'attachment'
                                }
                            },
                            noPreview : true,
                        },
                        status : 'r-hidden',
                        id : 'r-hidden',
                        url : {
                            type : 'r-preview',
                            previewType : 'document'
                        },
                    },
                },
                // metadata : {
                //     modelName : 'user'
                // }
            },
            fotos : {
                type : 'r-hasmany',
                hasmanyConf : {
                    fields : ['status','id','nome','descrizione','resource_id','url'],
                    fieldsConfig : {
                        resource_id : {
                            type : 'r-upload-ajax',
                            metadata : {
                                extensions : ['jpg','png'],
                                maxFileSize : '2M',
                                ajaxFields : {
                                    resource_type : 'foto'
                                }
                            },
                            noPreview : true,
                        },
                        url : {
                            type : 'r-preview',
                            previewType : 'image'
                        },
                        status : 'r-input',
                        id : 'r-hidden'

                    },
                },
                // metadata : {
                //     modelName : 'user'
                // }
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
    'list-edit' : {
        fieldsConfigEditMode : {
            'id' : 'text',
            'email' : 'text',
        }
    }
}