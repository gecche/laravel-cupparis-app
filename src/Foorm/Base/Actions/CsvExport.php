<?php

namespace Gecche\Cupparis\App\Foorm\Base\Actions;


use App\Models\CupparisEntity;
use App\Services\UploadService;
use Gecche\Cupparis\App\Enums\CupparisTipiCampi;
use Gecche\Cupparis\App\Services\FormatValues;
use Gecche\Foorm\FoormAction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CsvExport extends FoormAction
{

    protected $csvType;

    protected $csvSettings = [];
    protected $csvFieldsParams = [];

    protected $fields = [];

    protected $separator;
    protected $separatorReplacer;

    protected $endline;

    protected $csvModelName;

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


    protected function init()
    {
        parent::init();


        $this->formType = Arr::get($this->foorm->getConfig(), 'form_type');

        $this->csvType = Arr::get($this->input, 'csvType', 'default');
        $this->csvSettings = Arr::get($this->config, $this->csvType, []);
        $this->csvFieldsParams = Arr::get($this->csvSettings, 'fieldsParams', []);

        $this->separator = Arr::get($this->csvSettings, 'separator', ';');
        $this->separatorReplacer = Arr::get($this->csvSettings, 'separatorReplacer', ',');
        $this->endline = Arr::get($this->csvSettings, 'endline', "\n");
        $this->csvModelName = Str::snake($this->foorm->getModelRelativeName());

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

        $this->guessRelationFieldsHeaders();


    }

    public function getBoolLabel($configEntry)
    {
        return Arr::get($this->csvSettings, $configEntry, strtoupper($this->translitString(config('foorm.' . $configEntry))));
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
        return Arr::get($this->csvSettings,'dateFormat','d/m/Y');
    }

    public function getStandardDateTimeFormat() {
        return Arr::get($this->csvSettings,'dateTimeFormat','d/m/Y H:i:s');
    }

    protected function setCupparisEntity()
    {
        $snakeModelName = Arr::get($this->foorm->getConfig(), 'model');
        $relativeModelName = Str::studly($snakeModelName);
        $this->cupparisEntity = CupparisEntity::with('fields')->where('model_class', $relativeModelName)->first();

    }

    protected function setFields()
    {

        if (is_array(Arr::get($this->csvSettings, 'whitelist'))) {
            $this->fields = $this->csvSettings['whitelist'];
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
            $fieldParams = Arr::get($this->csvFieldsParams,$field,[]);
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


    protected function guessRelationFieldsHeaders() {


        foreach ($this->fields as $field) {

        }

    }


    public function getBlacklist()
    {
        $settingsBlacklist = Arr::get($this->csvSettings, 'blacklist');
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

    public function performAction()
    {


        $csvStream = '';
        if (Arr::get($this->csvSettings, 'headers', true)) {
            $csvStream .= $this->getCsvRowHeaders();
        }

        $transUc = trans_choice_uc('model.' . $this->csvModelName, 2);
        $relativeFilename = Str::replace([' ', '/'], ['_', '_'], $transUc)
            . '_' . date('Ymd_His') . ".csv";
        $filename = storage_temp_path($relativeFilename);
        File::append($filename, $csvStream);
        switch ($this->formType) {
            case 'list':
                $csvStream .= $this->performActionList($csvStream, $filename);
                break;
            default:
                break;
        }

        if ($this->isApi) {
            $name = $this->getApiFilename();
            $this->actionResult = [
                'content' => base64_encode(File::get($filename)),
                'mime' => 'text/csv',
                'name' => $name,
            ];
            return $this->actionResult;
        }

        $this->actionResult = ['link' => '/downloadtemp/' . $relativeFilename];
        return $this->actionResult;

    }


    public function getApiFilename()
    {
        $apiFilename = Arr::get($this->config, 'apiFilename', Str::replace("/", "_", Str::snake($this->foorm->getModelRelativeName())));

        return $apiFilename . '_' . date("Ymd_His") . ".csv";
    }


    protected function performActionList($csvStream, $filename = null)
    {
        $builder = $this->getFoormBuilder();
        $clonedBuilder = clone($builder);

        $i = 1;
        $perPage = 1000;
        $finito = false;
        while (!$finito) {

            $page = $i;

            $skip = ($page - 1) * $perPage;
            $builder = $builder->take($perPage)->skip($skip)->get();

//            $chunkData = $builder->toArray();

            if ($builder->count() >= 1) {
                $chunkStream = $this->getDataFromChunk($builder);
                $csvStream .= $chunkStream;

                $builder = $clonedBuilder;
                if ($filename) {
                    File::append($filename, $chunkStream);
                }
            } else {
                $finito = true;
            }
            $i++;
        }

        return $csvStream;
    }

    protected function getFoormBuilder()
    {
        return $this->foorm->getFormBuilder();
    }

    public function validateAction()
    {

        return true;

    }


    /*
         * Metodi per esportazione CSV
         */

    protected function getDataFromChunk($chunkData)
    {
        $csvChunkStream = '';

        foreach ($chunkData as $item) {
            $csvItem = $this->getCsvRow($item);
            $csvChunkStream .= rtrim(implode($this->separator, $csvItem), $this->separator) . $this->endline;
        }

        return $csvChunkStream;

    }

    public function getCsvRow($item)
    {
        $row = [];
        $itemArray = $item->toArray();
        $itemDotted = Arr::dot($itemArray);
        foreach ($this->fields as $key) {
            $methodKey = str_replace('|', '', $key);

            $itemValue = $this->guessItemValue($key, $itemDotted, $itemArray, $item);

            $methodName = 'getCsvField' . Str::studly($methodKey);
            if (method_exists($this, $methodName)) {
                $row[] = $this->$methodName($itemValue, $itemArray, $item);
            } else {
                $row[] = $this->getCsvFieldStandard($key, $itemValue, $itemArray, $item);
            }
        }
        return $row;
    }

    protected function guessItemValue($key, $itemDotted, $item, $itemObject)
    {

        if (array_key_exists('item', Arr::get($this->csvFieldsParams, $key, []))) {
            $itemKey = $this->csvFieldsParams[$key]['item'];
            if (array_key_exists($itemKey, $item))
                return $item[$itemKey];
            $itemKey = str_replace('|', '.', $itemKey);
            if (array_key_exists($itemKey, $itemDotted))
                return $itemDotted[$itemKey];
            return '';

        }

        $fieldKey = str_replace('|', '.', $key);
        $itemValue = Arr::get($itemDotted, $fieldKey);
        if (!$itemValue && array_key_exists($key, $item) && is_array($item[$key])) {
            $itemValue = $item[$key];
        }

        return $itemValue;
    }


    public function getCsvFieldStandard($key, $value, $item = [], $itemObject = null)
    {

        $guessedType = Arr::get($this->fieldsTypesGuessed, $key, 'string');
        switch ($guessedType) {

            case CupparisTipiCampi::DECIMAL->value:
            case CupparisTipiCampi::FLOAT->value:
                if (Arr::get($this->csvSettings, 'decimalTo')) {
                    $value = str_replace($this->csvSettings['decimalFrom'],
                        $this->csvSettings['decimalTo'],
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
        $value = Str::replace($this->separator, $this->separatorReplacer, $value);

        if (!$chars) {
            return $value;
        }

        $value = Str::replace($this->charsMappingIn, $this->charsMappingOut, $value);

        return $value;
    }

    public function getBoolValue($value)
    {
        if ($value) {
            return Arr::get($this->csvSettings, 'bool-true-label', config('foorm.bool-true-label'));
        }
    }


    /*
     * METODI STANDARD PER HEADERS (PLAIN - TRANSLATE)
     */

    public function getCsvRowHeaders()
    {

        $headersType = Arr::get($this->csvSettings, 'headers', 'translate');

        $methodName = 'getCsvRowHeaders' . Str::studly($headersType);
        $headers = $this->$methodName();
        return rtrim(implode($this->separator, $headers), $this->separator) . $this->endline;
    }

    public function checkHeaderMethod($key)
    {
        if (array_key_exists('header', Arr::get($this->csvFieldsParams, $key, []))) {
            return $this->csvFieldsParams[$key]['header'];
        }
        $methodName = 'getCsvHeader' . Str::studly($key);
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }
        return null;
        //VEDIAMO SE AGGIUNGERE ANCHE IL METODO SUL MODELLO
    }

    protected function getCsvRowHeadersPlain()
    {
        return array_map(function ($fieldKey) {
            $fieldHeader = $this->checkHeaderMethod($fieldKey);
            return $fieldHeader ?: $fieldKey;
        }, $this->fields);
    }

    protected function getCsvRowHeadersTranslate()
    {
        return array_map(function ($fieldKey) {
            $fieldHeader = $this->checkHeaderMethod($fieldKey);
            if ($fieldHeader) {
                return $fieldHeader;
            }
            $fieldKeyParts = explode('|', $fieldKey);
            if (count($fieldKeyParts) == 1) {
                return Lang::getMFormField($fieldKey, $this->csvModelName);
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
        }, $this->fields);
    }

    /*
     * // METODI STANDARD PER HEADERS (PLAIN - TRANSLATE)
     */


}
