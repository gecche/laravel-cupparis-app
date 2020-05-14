<?php

namespace Gecche\Cupparis\App\Breeze;


use Gecche\Cupparis\Datafile\Breeze\Concerns\BreezeDatafileTrait;
use Gecche\Cupparis\Datafile\Breeze\Concerns\HasDatafileValidation;

class BreezeDatafile extends Breeze {

    use HasDatafileValidation;
    use BreezeDatafileTrait;

	public $timestamps = false;
	// campi predefiniti, necessari per il funzionamento del modello
	public $datafile_id_field = 'datafile_id';
    public $row_index_field = 'row';

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
	);


}

// End Datafile Core Model
