<?php namespace Gecche\Cupparis\App\Models;

use App\Models\Relations;
use Gecche\Cupparis\App\Breeze\Breeze;
use App\Models\User;

/**
 * Breeze (Eloquent) model for queues table.
 */
class Queue extends Breeze
{
	use Relations\QueueRelations;


    
//    use ModelWithUploadsTrait;

    protected $table = 'queues';

    //protected $fillable = [];

    public $timestamps = true;
    public $ownerships = false;

    public $appends = [

    ];


    public static $relationsData = [
        //[self::BELONGS_TO, 'related' => CupGeoProvincia::class, 'table' => 'cup_geo_province', 'foreignKey' => 'provincia_id'],

        'user' => array(self::BELONGS_TO,'related' => User::class, 'foreignKey' => 'user_id'),

//        'belongsto' => array(self::BELONGS_TO, Queue::class, 'foreignKey' => '<FOREIGNKEYNAME>'),
//        'belongstomany' => array(self::BELONGS_TO_MANY, Queue::class, 'table' => '<TABLEPIVOTNAME>','pivotKeys' => [],'foreignKey' => '<FOREIGNKEYNAME>','otherKey' => '<OTHERKEYNAME>') ,
//        'hasmany' => array(self::HAS_MANY, Queue::class, 'table' => '<TABLENAME>','foreignKey' => '<FOREIGNKEYNAME>'),
    ];

    public static $rules = [
//        'username' => 'required|between:4,255|unique:users,username',
    ];

    public $columnsForSelectList = ['id'];
     //['id','descrizione'];

    public $defaultOrderColumns = ['id' => 'DESC'];
     //['cognome' => 'ASC','nome' => 'ASC'];

    public $columnsSearchAutoComplete = ['id'];
     //['cognome','denominazione','codicefiscale','partitaiva'];

    public $nItemsAutoComplete = 20;
    public $nItemsForSelectList = 100;
    public $itemNoneForSelectList = false;
    public $fieldsSeparator = ' - ';


}
