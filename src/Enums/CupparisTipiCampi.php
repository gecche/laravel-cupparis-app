<?php

namespace Gecche\Cupparis\App\Enums;

use Gecche\FSM\Contracts\FSMConfigInterface;
use Illuminate\Support\Arr;

enum CupparisTipiCampi: string
{
    use EnumHelperTrait;

    case STRING = 'string';
    case TEXT = 'text';
    case JSON = 'json';
    case INTEGER = 'integer';
    case ID_INTEGER = 'id_integer';
    case BOOLEAN = 'boolean';
    case ENUM = 'enum';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case DECIMAL = 'decimal';
    case FLOAT = 'float';


}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
