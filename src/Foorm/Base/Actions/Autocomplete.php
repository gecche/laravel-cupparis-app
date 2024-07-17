<?php

namespace Gecche\Cupparis\App\Foorm\Base\Actions;


use Gecche\Foorm\Actions\Autocomplete as BaseAutocomplete;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Autocomplete extends BaseAutocomplete
{

    protected function init()
    {
        parent::init();
        $field = Arr::get($this->input, 'field');
        $this->fieldToAutocomplete = Str::startsWith($field,'s_') ? substr($field,2) : $field;
    }

}
