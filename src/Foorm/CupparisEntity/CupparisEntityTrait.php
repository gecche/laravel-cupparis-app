<?php

namespace Gecche\Cupparis\App\Foorm\CupparisEntity;

use Illuminate\Support\Str;

trait CupparisEntityTrait {

    use CupparisEntityCommonTrait;


    public function createOptionsFieldsRelazioneTabella($fieldValue, $defaultOptionsValues, $relationName = null, $relationMetadata = [])
    {
        return array_combine($this->modelsList,$this->modelsList);
    }

    public function createOptionsColumnsList($fieldValue, $defaultOptionsValues, $relationName = null, $relationMetadata = [])
    {
        $fields = $this->model->fields->pluck('nome','nome')->all();
        return ['id' => 'id'] + $fields;
    }

    public function createOptionsColumnsOrder($fieldValue, $defaultOptionsValues, $relationName = null, $relationMetadata = [])
    {
        $fields = $this->model->fields->pluck('nome','nome')->all();
        $orderFields = [
          'id_asc' => 'id ASC',
            'id_desc' => 'id DESC',
        ];
        foreach ($fields as $fieldName) {
            $orderFields[$fieldName.'_asc'] = $fieldName . " ASC";
            $orderFields[$fieldName.'_desc'] = $fieldName . " DESC";
        }
        return $orderFields;
    }



}