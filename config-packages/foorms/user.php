<?php


/*
 * 'model' => <MODELNAME>
 * <FORMNAME> =>  [ //nome del form da route
 *      type => <FORMTYPE>, //tipo di form (opzionale se non c'è viene utilizzato il nome)
 *              //search, list, edit, insert, view, csv, pdf
 *      fields => [ //i campi del modello principale
 *          <FIELDNAME> => [
 *              'default' => <DEFAULTVALUE> //valore di default del campo (null se non presente)
 *              'options' => array|relation:<RELATIONNAME>:<COLUMNS>|dboptions|boolean
 *                          //le opzioni possibili di un campo, prese da un array associativo,
 *                              da una relazione (gli id del modello correlato
 *                              con <COLUMNS> serie di campi separati da virgola da inviare,
 *                              dal database (enum ecc...)
 *                              booleano
 *              'nulloption' => true|false|onchoice //onchoice indica che l'opzione nullable è presente solo se i valori
 *                                  delle options sono più di uno; default: true,
 *          ]
 *      ],
 *      relations => [ // le relazioni del modello principale
 *          <RELATIONNAME> => [
 *              fields => [ //i campi del modello principale
 *                  <FIELDNAME> => [
 *                      'default' => <DEFAULTVALUE> //valore di default del campo (null se non presente)
 *                      'options' => array|relation:<RELATIONNAME>|dboptions|boolean
 *                          //le opzioni possibili di un campo, prese da un array associativo,
 *                              da una relazione (gli id del modello correlato,
 *                              dal database (enum ecc...)
 *                              booleano
 *                      'nulloption' => true|false|onchoice //onchoice indica che l'opzione nullable è presente solo se i valori
 *                                    delle options sono più di uno; default: true,
 *                  ]
 *              ],
 *              savetype => [ //metodo di salvataggio della relazione
 *                              (in caso di edit/insert) da definire meglio
 *              ]
 *          ]
 *      ],
 *      params => [ // altri parametri opzionali
 *
 *      ],
 * ]
 */

return [

    'delete' => [

    ],

    'search' => [

        'fields' => [
            'email' => [
                'operator' => 'like',
            ],
            'name' => [
                'operator' => 'like',
            ],
            'roles|id' => [
                'options' => 'relation:roles',
            ],
        ]

    ],
    'list' => [

        'allowed_actions' => [

        ],

        'actions' => [
            'set' => [
                'allowed_fields' => [
                    'banned',
                    'email_verified_at',
                ],
            ],
        ],

        'dependencies' => [
          'search' => 'search',
        ],

        'pagination' => [
          'per_page' => 20,
            'pagination_steps' => [10,25,50,300],
        ],

        'fields' => [
            'id' => [
                'default' => 1,
            ],
            'name' => [

            ],
            'email' => [],
            'email_verified_at' => [],
            'banned' => [],
            'mainrole' => [],
//            'cliente_id' => [
//                'nullable' => true,
//                'options' => 'relation:cliente',
//            ],
        ],
        'relations' => [
            'fotos' => [
                'fields'  => [
                    'nome' => [],
                    'descrizione' => [],
                    'resource' => []
                ]

            ],
            'attachments' => [
                'fields'  => [
                    'nome' => [],
                    'descrizione' => [],
                    'resource' => []
                ]
            ],
        ],
        'params' => [

        ],
        'aggregates' => [
            'email' => [
                'count',
            ]
        ],
    ],
    'edit' => [
        'fields' => [
            'id' => [

            ],
            'name' => [
                //'default' => 'user'
            ],
//            'cliente_id' => [
//                'nullable' => true,
//                'options' => 'relation:cliente',
//            ],
            'banned' => [
                'options' => 'boolean',
                'nulloption' => false,
            ],
            'email' => [

            ],
            'password' => [

            ],
            'password_confirmation' => [

            ],
            'mainrole' => [
               'options' => 'relation:roles',
            ],
        ],
        'relations' => [
            'fotos' => [
                'fields' => [
                    'id' => [],
                    'nome' => [],
                    'descrizione' => [],
                    'resource' => [],
                ],
                //'saveType' => 'add',
                'orderKey' => 'ordine',

                'beforeNewCallbackMethods' => ['setFieldsFromResource'],
                'beforeUpdateCallbackMethods' => ['setFieldsFromResource'],
                'afterNewCallbackMethods' => ['filesOps'],
                'afterUpdateCallbackMethods' => ['filesOps'],
            ],
            'attachments' => [
                'fields' => [
                    'id' => [],
                    'nome' => [],
                    'descrizione' => [],
                    'resource' => [],
                ],
                //'saveType' => 'add',
                'orderKey' => 'ordine',

                'beforeNewCallbackMethods' => ['setFieldsFromResource'],
                'beforeUpdateCallbackMethods' => ['setFieldsFromResource'],
                'afterNewCallbackMethods' => ['filesOps'],
                'afterUpdateCallbackMethods' => ['filesOps'],
            ],

//            'tickets' => [
//                'fields' => [
//                    'id' => [
//
//                    ],
//                    'codice' => [
//                        'nullable' => true,
//                        'options' => 'relation:cliente',
//                        //'default' => 'pippo',
//                    ],
//                    'descrizione' => [
//
//                    ],
//                ],
//
//            ],
        ],
        'params' => [

        ],
    ],
//    'insert' => [
//
//    ],
    'view' => [
        'fields' => [
            'id' => [

            ],
            'name' => [
                //'default' => 'user'
            ],
//            'cliente_id' => [
//                'nullable' => true,
//                'options' => 'relation:cliente',
//            ],
            'banned' => [
                'options' => 'boolean',
                'nulloption' => false,
            ],
            'email' => [

            ],
            'password' => [

            ],
            'password_confirmation' => [

            ],
            'mainrole' => [
                'options' => 'relation:roles',
            ],
        ],
        'relations' => [
            'fotos' => [
                'fields' => [
                    'id' => [],
                    'nome' => [],
                    'descrizione' => [],
                    'resource' => [],
                ],
                //'saveType' => 'add',
                'orderKey' => 'ordine',

                'beforeNewCallbackMethods' => ['setFieldsFromResource'],
                'beforeUpdateCallbackMethods' => ['setFieldsFromResource'],
                'afterNewCallbackMethods' => ['filesOps'],
                'afterUpdateCallbackMethods' => ['filesOps'],
            ],
            'attachments' => [
                'fields' => [
                    'id' => [],
                    'nome' => [],
                    'descrizione' => [],
                    'resource' => [],
                ],
                //'saveType' => 'add',
                'orderKey' => 'ordine',

                'beforeNewCallbackMethods' => ['setFieldsFromResource'],
                'beforeUpdateCallbackMethods' => ['setFieldsFromResource'],
                'afterNewCallbackMethods' => ['filesOps'],
                'afterUpdateCallbackMethods' => ['filesOps'],
            ],
        ],
        'params' => [

        ],
    ],
];
