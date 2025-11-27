<?php

namespace Gecche\Cupparis\App\Enums;

use Gecche\FSM\Contracts\FSMConfigInterface;
use Illuminate\Support\Arr;

enum CupparisTipiWidgets: string
{
    use EnumHelperTrait;

    case WINPUT = 'w-input';
    case WSELECT = 'w-select';
    case WINPUTDATE = 'w-input-date';
    case WINPUTNUMBER = 'w-input-number';
    case WBELONGSTO = 'w-belongsto';
    case WEDITOR = 'w-editor';
    case WTEXTAREA = 'w-textarea';
    case WHIDDEN = 'w-hidden';
    case WSWAP = 'w-swap';
    case WTEXT = 'w-text';
    case WCUSTOM = 'w-custom';


}

// see https://emekambah.medium.com/php-enum-and-use-cases-in-laravel-ac015cf181ad
