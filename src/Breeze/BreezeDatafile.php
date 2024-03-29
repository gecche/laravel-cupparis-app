<?php

namespace Gecche\Cupparis\App\Breeze;


use Gecche\Cupparis\App\Models\DatafileError;
use Gecche\Cupparis\Datafile\Breeze\Concerns\BreezeDatafileTrait;
use Gecche\Cupparis\Datafile\Breeze\Concerns\HasDatafileValidation;
use Gecche\Cupparis\Datafile\Breeze\Contracts\DatafileBreezeInterface;
use Gecche\Cupparis\Datafile\Models\Datafile;

class BreezeDatafile extends Breeze implements DatafileBreezeInterface {

    use HasDatafileValidation;
    use BreezeDatafileTrait;

	public $timestamps = false;
	// campi predefiniti, necessari per il funzionamento del modello
	public $datafile_id_field = 'datafile_id';
    public $row_index_field = 'row';
    public $datafile_sheet_field = 'datafile_sheet';

    protected $guarded = [];

    public $headers;

	public static $relationsData = array(
		//'address' => array(self::HAS_ONE, 'Address'),
		//'orders'  => array(self::HAS_MANY, 'Order'),
		'errors' => [self::MORPH_MANY,
            'related' => DatafileError::class,
            'name' => 'datafile_table',
            'id' => 'datafile_table_id',
            'type' => 'datafile_table_type'
        ],

        'datafile' => [self::MORPH_ONE,
            'related' => Datafile::class,
            'name' => 'datafile',
            'id' => 'datafile_id',
            'type' => 'datafile_type'
        ],
	);


}

// End Datafile Core Model
