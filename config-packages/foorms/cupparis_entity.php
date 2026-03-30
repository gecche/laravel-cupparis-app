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

    'search' => [
        'fields' => [
            "nome" => [
                "operator" => "like"
            ],
        ],
    ],
    'list' => [
        'basic_query_fields' => ['nome'],
        'dependencies' => [
            'search' => 'search',
        ],

        'pagination' => [
            'per_page' => 20,
            'pagination_steps' => [10, 20, 50],
        ],

        'allowed_actions' => [
            'migrate' => true,
            'rollback' => true,
            'import' => true,
        ],

        'actions' => [
            'migrate' => [
            ],
            'rollback' => [
            ],
            'import' => [
            ],
        ],
        'fields' => [
            "nome" => [

            ],
            "model_class" => [

            ],
            "id" => [

            ],
            "cosa_migrare" => [
                'options' => [
                    'tutto' => 'Tutto',
                    'migration' => 'Migrazione',
                    'modello' => 'Modello',
                    'policy' => 'Policy',
                    'foorm' => 'Foorm',
                    'modelconf' => 'Modelconf',
                    'lang' => 'Lang',
                ],
//                'default' => ['tutto'],
                'nulloption' => false,
            ]
        ],
        'relations' => [

        ],
        'params' => [

        ],
    ],
    'edit' => [
        'fields' => [
            "nome" => [

            ],
            "model_class" => [

            ],
            "id" => [

            ],
            'lang_singolare' => [

            ],
            'lang_plurale' => [

            ],
            'columns_list' => [
                'options' => 'method',
            ],
            'columns_order' => [
                'options' => 'method',
            ],
            'has_foto' => [
                'options' => 'boolean',
                'nulloption' => false,
            ],
            'has_attachments' => [
                'options' => 'boolean',
                'nulloption' => false,
            ],
            'timestamps' => [
                'options' => ['nullable' => 'Nullable', 0 => 'No', 1 => 'Sì'],
                'nulloption' => false,
            ],
            'ownerships' => [
                'options' => ['nullable' => 'Nullable', 0 => 'No', 1 => 'Sì'],
                'nulloption' => false,
            ]
        ],
        'appends' => [
            'columns_list',
            'has_foto',
            'has_attachments',
        ],
        'relations' => [
            'fields' => [
                'fields' => [
                    'id' => [],
                    'nome' => [],
                    'tipo' => [
                        'options' => 'enum:' . \Gecche\Cupparis\App\Enums\CupparisTipiCampi::class,
                    ],
                    'informazioni' => [],
                    'nullable' => [
                        'options' => 'boolean',
                        'default' => 1,
                    ],
                    'relazione_tabella' => [
                        'options' => 'method',
                    ],
                    'relazione_campo' => [],
                    'default' => [

                    ],
                    'index' => [
                        'options' => [
                            'INDEX' => 'INDEX',
                            'UNIQUE' => 'UNIQUE',
                        ],
                    ],
                    'on_delete' => [
                        'options' => [
                            'cascade' => 'CASCADE',
                            'set_null' => 'SET NULL',
                            'restrict' => 'RESTRICT',
                        ],
                    ],
                    'on_update' => [
                        'options' => [
                            'cascade' => 'CASCADE',
                            'set_null' => 'SET NULL',
                            'restrict' => 'RESTRICT',
                        ],
                    ],
                    'model_conf_search' => [
                        'options' => 'enum:' . \Gecche\Cupparis\App\Enums\CupparisTipiWidgets::class,
                    ],
                    'model_conf_list' => [
                        'options' => 'enum:' . \Gecche\Cupparis\App\Enums\CupparisTipiWidgets::class,
                    ],
                    'model_conf_edit' => [
                        'options' => 'enum:' . \Gecche\Cupparis\App\Enums\CupparisTipiWidgets::class,
                    ],
                ]
            ]

        ],
        'params' => [

        ],
    ],
    'insert_express' => [
        'form_type' => 'insert',
        'fields' => [
            "id" => [

            ],
            "nome" => [

            ],
            "fields_text" => [

            ],
        ],
        'relations' => [

        ],
        'params' => [

        ],
    ],
//    'insert' => [
//
//    ],

];
