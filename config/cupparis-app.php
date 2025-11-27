<?php

return [

    'controllers-namespace' => "Gecche\\Cupparis\\App\\Http\\Controllers",

    /*
    |--------------------------------------------------------------------------
    | Uploads types and settings
    |--------------------------------------------------------------------------
    |
    |
    */

    'uploads' => [
        'foto' => [
            'max_size' => 2000,
            'exts' => 'jpeg,jpg,png',
        ],
        'attachment' => [
            'max_size' => 2000,
            'exts' => 'pdf,zip,rar,doc,docx,xls,xlsx,ppt,pptx,odf,ods,txt,csv',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Json error messages
    |--------------------------------------------------------------------------
    |
    |
    */

    'array_to_string' => true,
    'separator' => '<br/>',

    /*
     *
     */


    /*
     *
     */

    'cupparis_entity' => [


        'namespace' => 'App\\Http\\Controllers',

        'modelsPath' => 'app/Models/',
        'policiesPath' => 'app/Policies/',

        'models_namespace' => 'App\\Models\\',
        'policies_namespace' => 'App\\Policies\\',

        'modelConfsPath' => 'resources/vue-application-v4/src/application/ModelConfs/',

        'langs' => [
            'model' => [
                'lang/it/model.php',
            ],
            'fields' => [
                'lang/it/fields.php',
            ],
        ],

        'stubs' => [
            'migration' => 'stubs/migration/migration.stub',
            'model' => 'stubs/migration/model.stub',
            'modelconf' => 'stubs/migration/modelconf.stub',
            'config' => 'stubs/migration/config.stub',
            'fieldsTypesPath' => 'stubs/migration/modelsConfsFieldsTypes/',
            'policy' => 'stubs/migration/policy.stub',
            'foorm' => 'stubs/migration/foorm.stub',
        ],
    ],


];
