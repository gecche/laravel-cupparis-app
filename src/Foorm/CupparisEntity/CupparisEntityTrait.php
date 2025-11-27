<?php

namespace Gecche\Cupparis\App\Foorm\CupparisEntity;

use Illuminate\Support\Str;

trait CupparisEntityTrait {

    protected $modelsList = [];

    protected function getModelsList() {
        $filesModels = $this->files->files(app_path('Models'));
        $models = [];
        foreach ($filesModels as $file) {
            if (Str::endsWith($file, '.php')) {
                $name = $file->getRelativePathName();
                $model = substr($name, 0, -4);

                if (class_exists('App\\Models\\') . $model) {
                    $models[] = $model;
                }
            }
        }

        return $models;
    }



    public function createOptionsRelazioneTabella($fieldValue, $defaultOptionsValues, $relationName = null, $relationMetadata = [])
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