<?php

namespace Gecche\Cupparis\App\Models;

use Gecche\Cupparis\App\Breeze\Breeze;
use Gecche\Cupparis\DatafileJson\Breeze\Concerns\BreezeDatafileJsonTrait;
use Gecche\Cupparis\DatafileJson\Breeze\Concerns\HasDatafileJsonValidation;
use Gecche\Cupparis\DatafileJson\Breeze\Contracts\HasDatafileJsonValidationInterface;
use Gecche\Cupparis\DatafileJson\Models\Relations\DatafileJsonRowRelations;

class DatafileJsonRow extends Breeze implements HasDatafileJsonValidationInterface {

    use DatafileJsonRowRelations;
    use HasDatafileJsonValidation;
    use BreezeDatafileJsonTrait;

    protected $table = 'datafiles_json_rows';

    protected $guarded = ['id'];


    public $timestamps = true;
    public $ownerships = true;

    public $casts = [
        'row_data' => 'array',
    ];

    // campi predefiniti, necessari per il funzionamento del modello
    public $datafile_id_field = 'datafile_id';
    public $row_index_field = 'row';
    public $datafile_sheet_field = 'datafile_sheet';


    public $headers;

    public static $relationsData = array(
        //'address' => array(self::HAS_ONE, 'Address'),
        //'orders'  => array(self::HAS_MANY, 'Order'),

        'datafile' => [self::MORPH_ONE,
            'related' => DatafileJson::class,
            'name' => 'datafile',
            'id' => 'datafile_id',
            'type' => 'datafile_type'
        ],
    );



}
