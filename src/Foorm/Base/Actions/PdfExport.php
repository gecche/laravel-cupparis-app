<?php

namespace Gecche\Cupparis\App\Foorm\Base\Actions;


use Gecche\Foorm\FoormAction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class PdfExport extends FoormAction
{

    protected $pdfType;

    protected $pdfSettings = [];

    protected $fields = [];


    protected $pdfModelName;

    protected $formType;

    protected $relations = [];

    protected $builder;

    protected $fieldsWidths;
    protected $fieldsStyles;

    protected $labelsMethod;

    protected function init()
    {
        parent::init();


        $this->formType = Arr::get($this->foorm->getConfig(), 'form_type');

        $this->pdfType = Arr::get($this->input, 'pdfType', 'list');
        $this->pdfSettings = Arr::get($this->config, $this->pdfType, []);

        $this->pdfModelName = Str::snake($this->foorm->getModelRelativeName());

        $relationsData = ($this->foorm->getModelName())::getRelationsData();

        foreach ($relationsData as $relationName => $relation) {
            $relationModel = Str::replaceFirst($this->foorm->getModelsNamespace(), '',
                trim(Arr::get($relation, 'related', $relationName), "\\"));

            $this->relations[$relationName] = Str::snake($relationModel);
        }

        $this->setFields();

        $this->fieldsWidths = Arr::get($this->pdfSettings, 'fieldsWidths', []);
        $this->fieldsStyles = Arr::get($this->pdfSettings, 'fieldsStyles', []);

        $this->labelsMethod = Arr::get($this->pdfSettings, 'labelsMethod', 'translate');
    }


    protected function setFields()
    {

        if (is_array(Arr::get($this->pdfSettings, 'whitelist'))) {
            $this->fields = $this->pdfSettings['whitelist'];
            return;
        }

        $this->fields = $this->foorm->getFlatFields();

//        Log::info(print_r($attributes,true));
        if (is_array($this->pdfSettings['blacklist'])) {
            $this->fields = array_diff($this->fields, $this->pdfSettings['blacklist']);
        }

    }

    public function getFields()
    {
        if (is_null($this->fields)) {
            $this->setFields();
        }
        return $this->fields;
    }

    public function getPdfSettings()
    {
        return $this->pdfSettings;
    }

    public function getBuilder() {
        return $this->builder;
    }


    public function getFieldWidth($field)
    {
        $default = (int)(100 / (max(count($this->fields), 1)));
        return Arr::get($this->fieldsWidths, $field,
            Arr::get($this->pdfSettings, 'defaultFieldWidth', $default)
        );
    }

    public function getFieldStyle($field)
    {
        return Arr::get($this->fieldsStyles, $field,
            Arr::get($this->pdfSettings, 'defaultFieldStyle', '')
        );
    }

    public function performAction()
    {


        $transUc = trans_choice_uc('model.' . $this->pdfModelName, 2);
        $relativeFilename = str_replace(' ', '_', $transUc)
            . '_' . date('Ymd_His') . ".pdf";
        $filename = storage_temp_path($relativeFilename);

        $viewName = $this->getPdfView();
        $pdfOptions = $this->getPdfOptions();

        $this->builder = $this->foorm->getFormBuilder();

        $pdf = PDF::loadView($viewName, ['foormAction' => $this])->setOptions($pdfOptions)->output();

        File::append($filename, $pdf);

        $this->actionResult = ['link' => '/downloadtemp/' . $relativeFilename];
        return $this->actionResult;

    }


    public function validateAction()
    {

        return true;

    }


    public function getPdfView()
    {
        $viewName = Arr::get($this->pdfSettings, 'viewName', 'foorm-' . $this->pdfType);
        $viewPath = Arr::get($this->pdfSettings, 'viewPath', 'pdf');

        return $viewPath . '.' . $viewName;
    }

    public function getPdfOptions()
    {

        $pdfOptions = Arr::get($this->pdfSettings, 'options', []);
        $configLayout = Arr::get($this->pdfSettings, 'configLayout', $this->pdfType);
        $configPdfOptions = Config::get('snappy.layouts.' . $configLayout . '.options', []);

        $pdfOptions = array_merge($configPdfOptions, $pdfOptions);

        return $pdfOptions;
    }

    /*
         * Metodi per esportazione CSV
         */

    public function getPdfFieldStandard($key, $value)
    {
        if (is_numeric($value)) {
            if ($this->pdfSettings['decimalTo']) {
                $value = str_replace($this->pdfSettings['decimalFrom'],
                    $this->pdfSettings['decimalTo'],
                    $value);
            }
        } else {
            $value = str_replace(['"', "'", "\n", "\r"], '', $value);
        }
        return $value;

    }


    public function getPdfField($key,$itemDotted)
    {

            $fieldKey = str_replace('|', '.', $key);
            $itemValue = Arr::get($itemDotted, $fieldKey);
            $methodName = 'getPdfField' . Str::studly($key);
            if (method_exists($this, $methodName)) {
                return $this->$methodName($itemValue);
            }
            return $this->getPdfFieldStandard($key, $itemValue);
    }


    public function getPdfFieldLabel($fieldKey)
    {

        $methodName = 'getPdfFieldLabel' . Str::studly($this->labelsMethod);
        return $this->$methodName($fieldKey);
    }


    protected function getPdfFieldLabelPlain($fieldKey)
    {
        return $fieldKey;
    }

    protected function getPdfFieldLabelTranslate($fieldKey)
    {
        $fieldKeyParts = explode('|', $fieldKey);
        if (count($fieldKeyParts) == 1) {
            return Lang::getMFormField($fieldKey, $this->pdfModelName);
        }
        $relation = $fieldKeyParts[0];
        $field = $fieldKeyParts[1];
        $relationModel = Arr::get($this->relations, $relation, $relation);
        return trans_choice_uc('model.' . $relationModel, 1) .
            ' - ' . Lang::getMFormField($field, $relationModel);
    }

    /*
     * Fine metodi per esportazione CSV
    */


}
