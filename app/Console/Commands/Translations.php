<?php namespace App\Console\Commands;


class Translations extends \Gecche\Cupparis\App\Console\Commands\Translations
{
    protected $excludedModels = [
        'datafile_comune_istat',
    ];

}
