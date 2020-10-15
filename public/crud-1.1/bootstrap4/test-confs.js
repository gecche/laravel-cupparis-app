

function rInputConf() {
    renderConf = {
        value: 10,
        methods : {
            change : function() {
                console.log('my change',this.getValue())
            },
        },
        name : 'field1'
    };
    return renderConf;
}

function rPasswordConf() {
    renderConf = {
        value: 10,
        name : 'field1',
        inputType : 'password',
        methods : {
            change : function() {
                console.log('my change',this.getValue())
            },
        }

        // labels : {
        //     'app.add' : 'Aggiungi'
        // }
    };
    return renderConf
}
function rInputHelpedConf () {

    renderConf = {
        value : 15,
        name : 'field1',
        customValue : true,
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
        domainValues : {
            10 : "Dieci",
            20 : "Venti",
            30 : "Trenta",
        }
    };
    return renderConf;
}

function rAutocompleteConf() {
    renderConf = {
        //resources : ['https://use.fontawesome.com/releases/v5.8.2/css/all.css'],
        //resources : ['https://cdn.jsdelivr.net/gh/xcash/bootstrap-autocomplete@v2.2.2/dist/latest/bootstrap-autocomplete.min.js'],
        //resources : ['mdb.js'],
        value : 4,
        name : 'field1',
        routeName : 'autocomplete',
        fields : ['email','name'],
        modelName : 'user',
        methods : {
            change : function() {
                console.log('my conf change ' +  this.getValue())
            }
        },

    };
    return renderConf;
}

function rBelongstoConf() {
    renderConf = {
        name : 'field1',
        fields : ['testo'],
        value : {
            id : 1,
            testo : 'ciao1'
        }
    };
    return renderConf;
}

function rRadioConf() {
    renderConf = {
        value : 1,
        name : 'field1',

        domainValues :  {
            0 : 'Zero',
            1 : 'Uno',
            2 : 'Due',
            3 : 'Tre'
        },
        domainValuesOrder : [3,2,1,0],
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
    };
    return renderConf;
}


function rCheckboxConf() {
    renderConf = {
        value : [1,2],
        name : 'field1',
        domainValues :  {
            0 : 'Zero',
            1 : 'Uno',
            2 : 'Due',
            3 : 'Tre'
        },
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
    };
    return renderConf;
}



function rSelectConf () {
    renderConf = {
        value : 20,
        name : 'field1',
        domainValues : {
            10 : "Dieci",
            20 : "Venti",
            30 : "Trenta",
        },
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
    };
    return renderConf;
}

function rTextareaConf () {
    renderConf = {
        name : 'field1',
        value : "text area edit",
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
    };
    return renderConf;
}

function rTextConf() {
    renderConf = {
        name : 'field1',
        value : "testo edit",
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
    };
    return renderConf;
}

function rDateSelectConf() {
    renderConf = {
        name : 'field1',
        value : '2017-03-02',
        methods : {
            change: function () {
                console.log(this.getValue())
            }
        },
    };
    return renderConf;
}

function rDatePickerConf() {

    renderConf = {
        name : 'field1',
        value: "2018-02-04",
        //resources : ['bootstrap-datepicker/js/bootstrap-datepicker.js'],
        // resources : [
        //     'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js',
        //     'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css',
        //     'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js'
        // ],
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
    };
    return renderConf;
}

function rTexthtmlConf() {
    renderConf = {
        name : 'field1',
        value : "text area edit<b>bo</b>",
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
        // pluginOptions : {
        //     height:50,
        //     toolbar : [
        //         ['cleaner', ['cleaner']],
        //         ['style', ['style']],
        //         ['fontsize', ['fontsize']],
        //         ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
        //         ['color', ['color']],
        //         ['para', ['ul', 'ol', 'paragraph', 'height']],
        //         ['table', ['table']],
        //         ['media', ['link', 'picture', 'video']],
        //         // ['misc', ['codeview', 'fullscreen', 'help', 'undo', 'redo',
        //         //     'specialCharacter'
        //         // ]
        //     ],
        // }

    };
    return renderConf;
}

function rHasmanyConf() {
    renderConf = {
        name : 'field1',
        hasmanyConf: {
            fields: ['nome', 'descrizione','resource'],
            fieldsConfig : {
                resource : {
                    type : 'r-upload-ajax',
                    extensions : ['jpg','png'],
                    maxFileSize : '2M',
                    ajaxFields :  {
                        resource_type : 'foto',
                        field : 'resource'
                    },
                    modelName : 'user'
                }
            }
        },
        value : [],
        limit : 2,
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
    };
    return renderConf;
}

function rHasmanyViewConf() {
    renderConf = {
        name : 'field1',
        hasmanyConf: {
            fields: ['nome', 'descrizione','resource'],
            fieldsConfig : {
                resource : {
                    type : 'r-upload-ajax',
                    extensions : ['jpg','png'],
                    maxFileSize : '2M'
                }
            }
        },
        value : [
            {
                nome: null,
                descrizione: null,
                resource: {id: "files/foto/foto_1_1585222338.png", url: "/viewmediable/foto/1", mimetype: "image/png"}

            }

        ],
        limit : 2,
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
    };
    return renderConf;
}

function rSwapConf() {
    renderConf = {
        value : 5,
        name : 'activated',
        //swapType : 'text',
        modelData : {
            id : 4
        },

        domainValues : {
            5 : 'fa fa-times text-danger',
            6 : 'fa fa-gear text-success',
            7 : 'fa fa-question text-warning'
        },
        model : 'user',

        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
    };
    return renderConf;
}

function rHasmanyThroughConf() {

    renderConf = {
        value : [],
        name : 'field1',
        labelFields : ['stringa'],
        //addNew : true,
        metadata : {
            autocompleteModel : 'test'
        },
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
    };
    return renderConf;
}


function rB2Select2Conf() {
    renderConf = {
        value : null,
        name : 'field1',
        //addNew : true,
        fields : ['email'],
        //routeName : 'autocomplete',
        model : 'user',
        hiddenFields : ['id'],
        labelFields : ['email'],
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
    };
    return renderConf;
}

function rB2MSelect2Conf() {
    renderConf = {
        value : [],
        name : 'field1',
        //addNew : true,

        model : 'user',
        labelFields : ['email'],
        hiddenFields : ['id'],
        fields : ['email'],
        methods : {
            change : function() {
                console.log('change ' +  this.getValue())
            }
        },
    };
    return renderConf;
}


function rUploadConf() {
    renderConf = {
        value: null,
        name : 'field1',
        maxFileSize : "2M",
        extensions : ['jpg','jpeg','png'],
        methods : {
            change : function() {
                console.log('my change',this.getValue())
            },
        }
    };
    return renderConf;
}
function rUploadAjaxConf() {
    renderConf = {
        value: null,
        name : 'field1',
        maxFileSize : "2M",
        extensions : ['jpg','jpeg','png'],
        // campi da inviare nella richiesta ajax di upload file
        ajaxFields :  {
            //upload_type : 'attachment',
            resource_type : 'foto'
        },
        methods : {
            change : function() {
                console.log('my change',this.getValue())
            },
        }
    };
    return renderConf;
}


function vViewConf() {
    var vConf = app.confMerge(crud.conf.view,{
        //fields:['name'],
        actions : [],
        fieldsConfig : {
            mainrole : {
                type : 'r-belongsto',
                fields : ['name']
            }
            // fotos : {
            //     type : 'r-hasmany',
            //     hasmanyConf : {
            //         fields : ['id','nome','descrizione','resource','status'],
            //         fieldsConfig : {
            //             resource : 'r-upload-ajax',
            //             status : 'r-hidden'
            //         }
            //     }
            // },
            // attachments : {
            //     type : 'r-hasmany',
            //     hasmanyConf : {
            //         fields : ['id','nome','descrizione','resource','status'],
            //         fieldsConfig : {
            //             resource : {
            //                 type : 'r-upload-ajax',
            //                 metadata : {
            //                     ajaxFields: {
            //                         resource_type: 'attachment'
            //                     }
            //                 }
            //
            //             },
            //             status : 'r-hidden',
            //             id : 'r-hidden'
            //         }
            //     }
            //
            // }
        }
    });
    return vConf;
}

function vEditConf() {
    var vConf = app.confMerge(crud.conf.edit,{
        fieldsConfig : {
            fotos : {
                type : 'r-hasmany',
                hasmanyConf : {
                    fields : ['id','nome','descrizione','resource','status'],
                    fieldsConfig : {
                        resource : 'r-upload-ajax',
                        status : 'r-hidden'
                    }
                }
            },
            attachments : {
                type : 'r-hasmany',
                hasmanyConf : {
                    fields : ['id','nome','descrizione','resource','status'],
                    fieldsConfig : {
                        resource : {
                            type : 'r-upload-ajax',
                            metadata : {
                                ajaxFields: {
                                    resource_type: 'attachment'
                                }
                            }

                        },
                        status : 'r-hidden',
                        id : 'r-hidden'
                    }
                }

            }
        }
    });
    return vConf;
}

function vCustomConf() {
    return app.confMerge(crud.conf.edit,{
        routeName: null,
        fields : ['campo1','campo2'],
        data : {
            value : {
                campo1 : 'campo1Value',
                campo2 : 'campo2Value'
            },
            metadata : {

            }
        }
    })
}

function vCustomListConf() {
    return app.confMerge(crud.conf.list,{
        routeName: null,
        fields : ['campo1','campo2'],
        data : {
            value : [{
                campo1 : 'campo1Value',
                campo2 : 'campo2Value'
            },{
                campo1 : 'campo1Value1',
                campo2 : 'campo2Value2'
            },{
                campo1 : 'campo1Value1',
                campo2 : 'campo2Value2'
            },
            ],
            metadata : {

            }
        }
    })
}
