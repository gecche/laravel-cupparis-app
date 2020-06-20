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
    | App Vars default values
    |--------------------------------------------------------------------------
    |
    |
    */

    'vars' => [
//        'anno' => env('VARS_ANNO',2019),
    ],


];
