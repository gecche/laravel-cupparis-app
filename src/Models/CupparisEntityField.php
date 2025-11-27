<?php

namespace Gecche\Cupparis\App\Models;

use Gecche\Cupparis\App\Breeze\Breeze;

class CupparisEntityField extends Breeze
{
	use Relations\CupparisEntityFieldRelations;

    protected $table = 'cupparis_entities_fields';

    protected $guarded = ['id'];

    public $timestamps = true;
    public $ownerships = true;

    public $appends = [

    ];


    public static $relationsData = [

        'entity' => array(self::BELONGS_TO, 'related' => CupparisEntity::class, 'foreignKey' => 'entity_id'),



//        'belongsto' => array(self::BELONGS_TO, UnicoAttivita::class, 'foreignKey' => '<FOREIGNKEYNAME>'),
//        'belongstomany' => array(self::BELONGS_TO_MANY, UnicoAttivita::class, 'table' => '<TABLEPIVOTNAME>','pivotKeys' => [],'foreignKey' => '<FOREIGNKEYNAME>','otherKey' => '<OTHERKEYNAME>') ,
//        'hasmany' => array(self::HAS_MANY, UnicoAttivita::class, 'table' => '<TABLENAME>','foreignKey' => '<FOREIGNKEYNAME>'),
    ];

    public static $rules = [
//        'username' => 'required|between:4,255|unique:users,username',
    ];

    public $columnsForSelectList = ['nome'];
    //['id','descrizione'];

    public $defaultOrderColumns = ['nome' => 'ASC', ];
    //['cognome' => 'ASC','nome' => 'ASC'];

    public $columnsSearchAutoComplete = ['nome'];
    //['cognome','denominazione','codicefiscale','partitaiva'];

    public $nItemsAutoComplete = 20;
    public $nItemsForSelectList = 100;
    public $itemNoneForSelectList = false;
    public $fieldsSeparator = ' - ';
    //
}
