<?php

namespace Gecche\Cupparis\App\Foorm\Base\Actions;


use App\Models\CupparisEntity;
use App\Services\UploadService;
use Gecche\Cupparis\App\Enums\CupparisTipiCampi;
use Gecche\Cupparis\App\Services\FormatValues;
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
    protected $pdfFieldsParams = [];

    protected $fields = [];


    protected $pdfModelName;

    protected $formType;

    protected $relations = [];

    protected $cupparisEntity = null;

    protected $fieldsTypesGuessed;

    protected $boolTrueLabel, $boolFalseLabel;

    protected $charsMappingIn = [
        'à', 'ì', 'è', 'é', 'ò', 'ù'
    ];
    protected $charsMappingOut = [
        'a', 'i', 'e', 'e', 'o', 'u'
    ];

    protected $dateFormat, $dateTimeFormat;

    protected $fieldsPrefixes = [];
    protected $fieldsSuffixes = [];


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
        $this->pdfFieldsParams = Arr::get($this->pdfSettings, 'fieldsParams', []);

        $this->pdfModelName = Str::snake($this->foorm->getModelRelativeName());

        $relationsData = ($this->foorm->getModelName())::getRelationsData();

        foreach ($relationsData as $relationName => $relation) {
            $relationModel = Str::replaceFirst($this->foorm->getModelsNamespace(), '',
                trim(Arr::get($relation, 'related', $relationName), "\\"));

            $this->relations[$relationName] = Str::snake($relationModel);
        }


        $this->boolTrueLabel = $this->getBoolTrueLabel();
        $this->boolFalseLabel = $this->getBoolFalseLabel();
        $this->dateFormat = $this->getStandardDateFormat();
        $this->dateTimeFormat = $this->getStandardDateTimeFormat();

        $this->setCupparisEntity();
        $this->setFields();

        $this->guessFieldsTypes();

        $this->setFieldsPrefixesSuffixes();

        $this->fieldsWidths = Arr::get($this->pdfSettings, 'fieldsWidths', []);
        $this->fieldsStyles = Arr::get($this->pdfSettings, 'fieldsStyles', []);

        $this->labelsMethod = Arr::get($this->pdfSettings, 'labelsMethod', 'translate');
    }

    public function getBoolLabel($configEntry)
    {
        return Arr::get($this->pdfSettings, $configEntry, strtoupper($this->translitString(config('foorm.' . $configEntry))));
    }

    public function getBoolFalseLabel()
    {
        return $this->getBoolLabel('bool-false-label');
    }

    public function getBoolTrueLabel()
    {
        return $this->getBoolLabel('bool-true-label');
    }

    public function getStandardDateFormat() {
        return Arr::get($this->pdfSettings,'dateFormat','d/m/Y');
    }

    public function getStandardDateTimeFormat() {
        return Arr::get($this->pdfSettings,'dateTimeFormat','d/m/Y H:i:s');
    }

    protected function setCupparisEntity()
    {
        $snakeModelName = Arr::get($this->foorm->getConfig(), 'model');
        $relativeModelName = Str::studly($snakeModelName);
        $this->cupparisEntity = CupparisEntity::with('fields')->where('model_class', $relativeModelName)->first();

    }

    protected function setFields()
    {

        if (is_array(Arr::get($this->pdfSettings, 'whitelist'))) {
            $this->fields = $this->pdfSettings['whitelist'];
            return;
        }

        $this->fields = $this->getFlatFields();

        $blacklist = $this->getBlacklist();
        $this->fields = array_diff($this->fields, $blacklist);


    }

    public function getFlatFields() {
        $fields = $this->foorm->getFlatFields('field');
        $fields = array_merge($fields,$this->foorm->getFlatFields('relationfield'));
        return $fields;
    }

    protected function guessFieldsTypes() {
        $this->fieldsTypesGuessed = array_fill_keys($this->fields, 'string');

        foreach ($this->fields as $field) {
            $fieldParams = Arr::get($this->pdfFieldsParams,$field,[]);
            if (array_key_exists('type',$fieldParams)) {
                $this->fieldsTypesGuessed[$field] = $fieldParams['type'];
            } elseif ($this->cupparisEntity) {
                $cupparisField = $this->cupparisEntity->fields->where('nome', $field)->first();
                if ($cupparisField) {
                    $this->fieldsTypesGuessed[$field] = $cupparisField->tipo;
                }
            }
        }
    }


    public function getBlacklist()
    {
        $settingsBlacklist = Arr::get($this->pdfSettings, 'blacklist');
        return is_array($settingsBlacklist) ? $settingsBlacklist : $this->getStandardBlacklist();
    }

    public function getStandardBlacklist()
    {

        $blacklist = config('foorm.standard_export_blacklist', [
            'id',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'info',
            'status_history',
        ]);
        if (config('foorm.standard_export_blacklist_relation_ids',true)) {
            foreach ($this->fields as $field) {
                if (Str::endsWith($field,'_id')) {
                    $blacklist[] = $field;
                }
            }
        }
        return $blacklist;
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

    public function setBuilder() {
        try {
            $this->builder = $this->foorm->getFormBuilder();
        } catch (\Throwable $e) {
            $this->builder = $this->model->where($this->model->getKeyName(),$this->model->getKey());
        }
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



        $relativeFilename = $this->getRelativeExportFilename();
        $filename = $this->getAbsoluteExportFilename($relativeFilename);

        $viewName = $this->getPdfView();
        $pdfOptions = $this->getPdfOptions();

        $this->setBuilder();

        $pdf = PDF::loadView($viewName, ['foormAction' => $this])->setOptions($pdfOptions)->output();

        File::append($filename, $pdf);

        if ($this->isApi) {
            $name = $this->getRelativeExportFilename(true);
            $this->actionResult = [
                'content' => base64_encode(File::get($filename)),
                'mime' => 'application/pdf',
                'name' => $name,
            ];
            return $this->actionResult;
        }

        $this->actionResult = ['link' => '/downloadtemp/' . $relativeFilename];
        return $this->actionResult;

    }

    protected function getRelativeExportFilenamePrefix() {
        return Arr::get($this->pdfSettings, 'filename',trans_choice_uc('model.' . $this->pdfModelName, 2));
    }

    protected function getRelativeExportApiFilenamePrefix() {
        return Arr::get($this->pdfSettings, 'apiFilename',$this->getRelativeExportFilenamePrefix());
    }

    public function getRelativeExportFilename($api = false) {
        $prefix = $api ? $this->getRelativeExportApiFilenamePrefix() : $this->getRelativeExportFilenamePrefix();
        return Str::replace([' ', '/'], ['_', '_'], $prefix)
            . '_' . date('Ymd_His') . ".pdf";
    }

    public function getAbsoluteExportFilename($relativeFilename) {
        return storage_temp_path($relativeFilename);
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

    protected function guessItemValue($key, $itemDotted, $item, $itemObject)
    {

        $fieldsParams = Arr::get($this->pdfFieldsParams, $key, []);
        if (array_key_exists('fixed', $fieldsParams)) {
            return  $fieldsParams['fixed'];
        }

        if (array_key_exists('item', $fieldsParams)) {
            $itemKey = $fieldsParams['item'];
            if (array_key_exists($itemKey, $item))
                return $item[$itemKey];
            $itemKey = str_replace('|', '.', $itemKey);
            if (array_key_exists($itemKey, $itemDotted))
                return $itemDotted[$itemKey];
            return '';

        }

        $fieldKey = str_replace('|', '.', $key);
        $itemValue = Arr::get($itemDotted, $fieldKey);
        if (!$itemValue && array_key_exists($key,$item) && is_array($item[$key])) {
            $itemValue = $item[$key];
        }

        return $itemValue;
    }


    public function getPdfFieldStandard($key, $value, $item = [], $itemObject = null)
    {

        $guessedType = Arr::get($this->fieldsTypesGuessed, $key, 'string');
        switch ($guessedType) {

            case CupparisTipiCampi::DECIMAL->value:
            case CupparisTipiCampi::FLOAT->value:
                if (Arr::get($this->pdfSettings, 'decimalTo')) {
                    $value = str_replace(Arr::get($this->csvSettings, 'decimalFrom', '.'),
                        $this->pdfSettings['decimalTo'],
                        $value);
                }
                return $value;
            case CupparisTipiCampi::BOOLEAN->value:
                return $value ? $this->boolTrueLabel : $this->boolFalseLabel;
            case CupparisTipiCampi::DATE->value:
                return FormatValues::formatDate($value,$this->dateFormat);
            case CupparisTipiCampi::DATETIME->value:
                return FormatValues::formatDate($value,$this->dateTimeFormat);
            default:
                if (is_array($value)) {
                    return cupparis_json_encode($value);
                }
                return $this->translitString($value);
        }

    }

    public function translitString($value, $chars = true)
    {
        $value = Str::replace(['"', "'", "\n", "\r"], '', $value);
//        $value = Str::replace($this->separator, $this->separatorReplacer, $value);

        if (!$chars) {
            return $value;
        }

        $value = Str::replace($this->charsMappingIn, $this->charsMappingOut, $value);

        return $value;
    }

    public function getBoolValue($value)
    {
        if ($value) {
            return Arr::get($this->pdfSettings, 'bool-true-label', config('foorm.bool-true-label'));
        }
    }

    public function getPdfField($key,$item)
    {

        $itemArray = $item->toArray();
        $itemDotted = \Illuminate\Support\Arr::dot($itemArray);


        $itemValue = $this->guessItemValue($key,$itemDotted,$itemArray,$item);
        $methodName = 'getPdfField' . Str::studly($key);
        if (method_exists($this, $methodName)) {
            $pdfFieldValue = $this->$methodName($itemValue);
        } else {
            $pdfFieldValue = $this->getPdfFieldStandard($key, $itemValue);
        }

        return $this->checkAndSetPrefixSuffix($key,$pdfFieldValue);
    }

    protected function setFieldsPrefixesSuffixes() {

        foreach ($this->fields as $key) {
            $fieldsParams = Arr::get($this->pdfFieldsParams, $key, []);
            foreach (['prefix' => 'fieldsPrefixes','suffix' => 'fieldsSuffixes'] as $mutator => $mutatorsArray) {
                if (array_key_exists($mutator,$fieldsParams)) {
                    $this->$mutatorsArray[$key] = $fieldsParams[$mutator];
                } else {
                    $this->$mutatorsArray[$key] = null;
                }
            }
        }

    }

    protected function checkAndSetPrefixSuffix($key,$itemValue) {

        if ($itemValue) {
            if (!is_null($this->fieldsPrefixes[$key])) {
                $itemValue = $this->fieldsPrefixes[$key] . $itemValue;
            }
            if (!is_null($this->fieldsSuffixes[$key])) {
                $itemValue = $itemValue . $this->fieldsSuffixes[$key];
            }
        }
        return $itemValue;
    }



    public function getPdfFieldLabel($fieldKey)
    {

        if (array_key_exists('header', Arr::get($this->pdfFieldsParams, $fieldKey, []))) {
            return $this->pdfFieldsParams[$fieldKey]['header'];
        }
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
        $relationPrefix = $relation . '|';
        $relationFields = array_filter($this->fields,function ($item) use ($relationPrefix)  {
            return Str::startsWith($item,$relationPrefix);
        });

        if (count($relationFields) > 1) {
            return trans_choice_uc('model.' . $relationModel, 1) .
                ' - ' . Lang::getMFormField($field, $relationModel);
        } else {
            return trans_choice_uc('model.' . $relationModel, 1);
        }

    }

    public function getPdfTitle($type = null) {

        if (Arr::get($this->pdfSettings,'documentTitle')) {
            return $this->pdfSettings['documentTitle'];
        }


        switch ($type) {
            case 'list':
                return trans_choice_uc('model.' . $this->pdfModelName, 2);
            case 'record':
                return trans_choice_uc('model.' . $this->pdfModelName, 1) . ' - '
                    . ucfirst($this->model->getKeyName()) . ': ' . $this->model->getKey();
            default:
                return "";
        }

    }
    /*
     * Fine metodi per esportazione CSV
    */


}
