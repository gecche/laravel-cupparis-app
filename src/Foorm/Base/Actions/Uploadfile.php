<?php

namespace Gecche\Cupparis\App\Foorm\Base\Actions;


use App\Services\UploadService;
use Gecche\Foorm\FoormAction;
use Illuminate\Support\Arr;

class Uploadfile extends FoormAction
{

    protected $uploadService;

    protected $fieldToUpload;

    protected $file;

    protected $resourceType;

    protected function init()
    {
        parent::init();

        $this->fieldToUpload = Arr::get($this->input, 'field');
        $this->file = Arr::get($this->input, 'file');

        $this->uploadService = UploadService::getInstance();
    }


    public function performAction()
    {

        $result = $this->uploadFile();

        $this->actionResult = $result;

        return $this->actionResult;

    }


    protected function uploadFile() {

        return $this->uploadService->saveTempFile($this->resourceType, $this->file);

    }

    public function validateAction()
    {

        $this->validateField();

    }


    protected function validateField()
    {

        $field = $this->fieldToUpload;
        if (!$field) {
            throw new \Exception("The uploadfile action needs a field to be uploaded");
        }


        if (!$this->file) {
            throw new \Exception("The uploadfile action needs a file to be uploaded");
        }

        if (!$this->foorm->hasFlatField($field)) {
            throw new \Exception("The field " . $field . " to be uploaded is not configured in this foorm");
        }

        $fieldConfig = Arr::get(Arr::get($this->config, 'fields', []), $field, []);

        $this->resourceType = Arr::get($fieldConfig, 'resource_type', 'foto');

        $this->uploadService->setUploadValidationRules($this->resourceType,Arr::get($fieldConfig,'max_size')
            ,Arr::get($fieldConfig,'exts'));
        $this->uploadService->validate($this->resourceType, ['file' => $this->file]);


    }
}
