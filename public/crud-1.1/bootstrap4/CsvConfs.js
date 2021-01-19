var ComuneIstat = {
    confUpload : {
        value: null,
        name: "resource",
        maxFileSize: "2M",
        modelName : 'datafile_comune_istat',
        extensions: [
            "csv",
        ],
        ajaxFields: {
            resource_type: "attachment",
            field : 'comune_istat|resource',
        }
    }
}

var CupGeoNazioniIstat = {
    providerName : 'cup_geo_nazioni_istat',
    cRef : 'csvD',
    // confUpload : {
    //     value: null,
    //     name: "resource",
    //     maxFileSize: "2M",
    //     modelName : 'cup_geo_nazioni_istat',
    //     extensions: [
    //         "csv",
    //     ],
    //     ajaxFields: {
    //         //resource_type: "attachment",
    //         field : 'resource',
    //     }
    // },
    viewUpload : {
        fields : ['nome','tipo'],
        actions : ['action-save','action-cancel'],
    },
    viewSave : {
        fields : ['nomeSave','tipoSave'],
    },
    viewList : {

    }
}
