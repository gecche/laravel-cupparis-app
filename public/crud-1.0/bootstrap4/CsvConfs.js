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
