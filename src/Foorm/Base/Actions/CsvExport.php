<?php

namespace Gecche\Cupparis\App\Foorm\Base\Actions;


use App\Services\UploadService;
use Gecche\Foorm\FoormAction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class CsvExport extends FoormAction
{

    protected $csvType;

    protected $csvSettings = [];

    protected $fields = [];

    protected $separator;

    protected $endline;

    protected $csvModelName;

    protected $formType;

    protected function init()
    {
        parent::init();


        $this->formType = Arr::get($this->foorm->getConfig(),'form_type');

        $this->csvType = Arr::get($this->input, 'csvType', 'default');
        $this->csvSettings = Arr::get($this->config, $this->csvType, []);

        $this->separator = Arr::get($this->csvSettings, 'separator', ';');
        $this->endline = Arr::get($this->csvSettings, 'endline', "\n");
        $this->csvModelName = Str::snake($this->foorm->getModelRelativeName());

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

        switch ($this->formType) {
            case 'list':
                $csvStream = $this->performActionList($csvStream);
                break;
            default:
                break;
        }


        $this->actionResult = $csvStream;
        return $this->actionResult;

    }


    protected function performActionList($csvStream) {
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
                $csvStream .= $this->getDataFromChunk($chunkData);

                $builder = $clonedBuilder;
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

    public function getCsvFieldStandard($key,$value)
    {
        if ($this->csvSettings['decimalTo'] && is_numeric($value)) {
            return str_replace($this->csvSettings['decimalFrom'],
                $this->csvSettings['decimalTo'],
                $value);
        }
        return $value;
    }

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
        foreach ($this->fields as $key) {

            $itemDotted = Arr::dot($item);
            $fieldKey = str_replace('|','.',$key);
            $itemValue = Arr::get($itemDotted,$fieldKey);
            $methodName = 'getCsvField' . Str::studly($key);
            if (method_exists($this, $methodName)) {
                 $row[] = $this->$methodName($itemValue);
            } else {
                $row[] = $this->getCsvFieldStandard($key,$itemValue);
            }
        }
        return $row;
    }

    public function getCsvRowHeaders()
    {

        $headersType = Arr::get($this->csvSettings, 'headers', 'translate');
        switch ($headersType) {
            case 'plain':
                $headers = $this->getCsvRowHeadersStandard(false);
                break;
            case 'translate':
                $headers = $this->getCsvRowHeadersStandard(true);
                break;
            default:
                $methodName = 'getCsvRowHeaders' . Str::studly($headersType);
                $headers = $this->$methodName();
                break;
        }
        return rtrim(implode($this->separator, $headers), $this->separator) . $this->endline;
    }

    public function checkHeaderMethod($key)
    {
        $methodName = 'getCsvHeader' . Str::studly($key);
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }
        return null;
        //VEDIAMO SE AGGIUNGERE ANCHE IL METODO SUL MODELLO
    }

    protected function getCsvRowHeadersStandard($translate)
    {
        return array_map(function ($fieldKey) use ($translate) {
            $fieldHeader = $this->checkHeaderMethod($fieldKey);
            return $fieldHeader ?: ($translate ? Lang::getMFormField($fieldKey, $this->csvModelName) : $fieldKey);
        }, $this->fields);
    }


    public static function getCsvExport($models = null, $modelParams = [], $type = 'default', $params = [])
    {
        if (is_null($models)) {
            $models = static::all();
        }

        $csvStream = '';

        $newModel = new static;

        $writeHeader = true;
        if (
            !Arr::get($newModel->csvExportSettings[$type], 'headers', true) ||
            !Arr::get($params, 'headers', true)
        ) {
            $writeHeader = false;
        }
        if ($writeHeader) {
            $csvStream .= $newModel->getCsvRowHeadersExport($type, $modelParams);
        }

        foreach ($models as $model) {
            $csvStream .= $model->getCsvRowExport($type, $modelParams);
        }

        return $csvStream;
    }

    /*
     * Fine metodi per esportazione CSV
    */


}
