<?php

namespace Gecche\Cupparis\App\Foorm\Base\Actions;


use App\Services\UploadService;
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
            $relationModel = Str::replaceFirst($this->foorm->getModelsNamespace(),'',
                trim(Arr::get($relation,'related',$relationName),"\\"));

            $this->relations[$relationName] = Str::snake($relationModel);
        }

        $this->setFields();

    }


    protected function setFields()
    {

        if (is_array(Arr::get($this->csvSettings, 'whitelist'))) {
            $this->fields = $this->csvSettings['whitelist'];
            return;
        }

        $this->fields = $this->foorm->getFlatFields();

//        Log::info(print_r($attributes,true));
        if (is_array($this->csvSettings['blacklist'])) {
            $this->fields = array_diff($this->fields, $this->csvSettings['blacklist']);
        }

    }


    public function performAction()
    {


        $csvStream = '';
        if (Arr::get($this->csvSettings, 'headers', true)) {
            $csvStream .= $this->getCsvRowHeaders();
        }

        $transUc = trans_choice_uc('model.' . $this->csvModelName, 2);
        $relativeFilename = str_replace(' ', '_', $transUc)
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

        $this->actionResult = ['link' => '/downloadtemp/' . $relativeFilename];
        return $this->actionResult;

    }


    protected function performActionList($csvStream, $filename = null)
    {
        $builder = $this->foorm->getFormBuilder();
        $clonedBuilder = clone($builder);

        $i = 1;
        $perPage = 1000;
        $finito = false;
        while (!$finito) {

            $page = $i;

            $skip = ($page - 1) * $perPage;
            $builder = $builder->take($perPage)->skip($skip)->get();

            $chunkData = $builder->toArray();

            if (count($chunkData) >= 1) {
                $chunkStream = $this->getDataFromChunk($chunkData);
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
        $itemDotted = Arr::dot($item);
        foreach ($this->fields as $key) {
            $methodKey = str_replace('|', '', $key);

            $itemValue = $this->guessItemValue($key,$itemDotted,$item);

            $methodName = 'getCsvField' . Str::studly($methodKey);
            if (method_exists($this, $methodName)) {
                $row[] = $this->$methodName($itemValue);
            } else {
                $row[] = $this->getCsvFieldStandard($key, $itemValue);
            }
        }
        return $row;
    }

    protected function guessItemValue($key,$itemDotted,$item) {

        if (array_key_exists('item',Arr::get($this->csvFieldsParams,$key,[]))) {
            return $item[$this->csvFieldsParams[$key]['item']];
        }

        $fieldKey = str_replace('|', '.', $key);
        $itemValue = Arr::get($itemDotted, $fieldKey);
        if (!$itemValue && array_key_exists($key,$item) && is_array($item[$key])) {
            $itemValue = $item[$key];
        }

        return $itemValue;
    }


    public function getCsvFieldStandard($key, $value)
    {
        if (is_numeric($value)) {
            if ($this->csvSettings['decimalTo']) {
                $value = str_replace($this->csvSettings['decimalFrom'],
                    $this->csvSettings['decimalTo'],
                    $value);
            }
        } else {
            $value = str_replace(['"',"'","\n","\r"], '', $value);
            $value = str_replace($this->separator, $this->separatorReplacer, $value);
//            return str_replace($this->separator,'"'.$this->separator.'"',$value);
        }
        return $value;

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
        if (array_key_exists('header',Arr::get($this->csvFieldsParams,$key,[]))) {
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
        return array_map(function ($fieldKey)  {
            $fieldHeader = $this->checkHeaderMethod($fieldKey);
            return $fieldHeader ?: $fieldKey;
        }, $this->fields);
    }

    protected function getCsvRowHeadersTranslate()
    {
        return array_map(function ($fieldKey)  {
            $fieldHeader = $this->checkHeaderMethod($fieldKey);
            if ($fieldHeader) {
                return $fieldHeader;
            }
            $fieldKeyParts = explode('|',$fieldKey);
            if (count($fieldKeyParts) == 1) {
                return Lang::getMFormField($fieldKey, $this->csvModelName);
            }
            $relation = $fieldKeyParts[0];
            $field = $fieldKeyParts[1];
            $relationModel = Arr::get($this->relations,$relation,$relation);
            return trans_choice('model.'.$relationModel,1) .
                ' - ' . Lang::getMFormField($field, $relationModel);
        }, $this->fields);
    }

    /*
     * // METODI STANDARD PER HEADERS (PLAIN - TRANSLATE)
     */




}
