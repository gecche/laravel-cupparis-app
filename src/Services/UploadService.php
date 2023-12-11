<?php

namespace Gecche\Cupparis\App\Services;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;


/*
 * CLASSE PER GESTIRE LA FASE DI UPLOADI UN FILE.
 * EVENTUALMENTE DA FARE COME PROVIDER E FACADE IN FUTURO.
 * PER ORA SERVIZIO AL VOLO COME SINGLETON.
 */

class UploadService {


    protected $config = [];

    protected $validationRules = [];

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @staticvar Singleton $instance The *Singleton* instances of this class.
     *
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
        $this->config = config('cupparis-app.uploads',[]);

    }

    public function setUploadValidationRules($type,$maxSize = null,$exts = null) {

        $maxFileSize = $this->getMaxSize($type,$maxSize);


        $methodName = 'setUploadValidationRules'.Str::studly($type);

        return $this->$methodName($maxFileSize,$exts);
    }
    public function getUploadValidationRules($type,$maxSize = null,$exts = null) {

        if (is_array($this->validationRules) && array_key_exists($type,$this->validationRules) )  {
            return $this->validationRules[$type];
        }

        $this->setUploadValidationRules($type,$maxSize, $exts);

        return $this->validationRules[$type];

    }


    protected function getMaxSize($type,$maxSize = null) {
        $maxSystemSize = ini_get('upload_max_filesize');
        if (Str::endsWith($maxSystemSize,'M')) {
            $maxSystemSize = substr($maxSystemSize,0,-1);
            $maxSystemSize = intval($maxSystemSize) * 1000;
        } elseif (is_numeric($maxSystemSize)) {
            $maxSystemSize = (int) ($maxSystemSize / 1024);
        }

        if ($maxSize) {
            $typeMaxSize = $maxSize;
        } else {
            $typeConfig = $this->getConfig($type);

            $typeMaxSize = Arr::get($typeConfig,'max_size',$maxSystemSize);
        }

        return ($typeMaxSize > $maxSystemSize) ? $maxSystemSize : $typeMaxSize;

    }

    /**
     * @return array|\Illuminate\Config\Repository|mixed
     */
    public function getConfig($type = null)
    {
        if (is_null($type))
            return $this->config;

        return Arr::get($this->config,$type,[]);
    }

    protected function setUploadValidationRulesFoto($maxFileSize,$exts = null) {
        $allowedExts = $exts ?: Arr::get($this->getConfig('foto'),'exts','jpeg,bmp,png');
        $rules = [
            'max:'.$maxFileSize,
            'mimes:'.$allowedExts,
        ];
        $this->validationRules['foto'] = [
            'file' => implode('|',$rules),
        ];
    }

    protected function setUploadValidationRulesAttachment($maxFileSize,$exts = null) {
        $allowedExts = $exts ?: Arr::get($this->getConfig('attachment'),'exts','pdf,zip,rar,doc,docx,xls,xlsx,ppt,pptx,odf,ods,txt,csv');
        if (is_array($allowedExts)) {
            $allowedExts = implode(",",$allowedExts);
        }
        $rules = [
            'max:'.$maxFileSize,
            'mimes:'.$allowedExts,
        ];
        $this->validationRules['attachment'] = [
            'file' => implode('|',$rules),
        ];
    }


    public function validate($type,$input,$throwException = true) {

        $validation_rules = $this->getUploadValidationRules($type);

        $validator = Validator::make($input, $validation_rules);



        if (!$validator->passes()) {
            if ($throwException) {
                throw new \Exception($validator->errors());
            }
            return $validator->errors();
        }

        return true;

    }

    public function saveTempFile($type, $file) {

        $methodName = 'saveTempFile' . Str::studly($type);

        if (method_exists($this,$methodName)) {
            return $this->$methodName($file);
        }

        $temp_dir = storage_temp_path();
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir);
        }
        $file_prefix = 'temp_' . $type . '_';
        $carbon = Carbon::now();
        $random = $carbon->timestamp . rand(1, 100000);

        $ext = $file->getClientOriginalExtension();

        $tempFileName = $file_prefix . $random . '.' . $ext;

        $file->move($temp_dir, $tempFileName);

        return $this->getTempFileArray($type,$file,$tempFileName);


    }

    public function getTempFileArray($type,$file,$tempFileName) {

        $methodName = 'getTempFileArray' . Str::studly($type);

        if (method_exists($this,$methodName)) {
            return $this->$methodName($file,$tempFileName);
        }

// TODO creare una copia per copy()
        return [
            'id' => $tempFileName,
            'mimetype' => $file->getClientMimeType(),
            'filename' => $file->getClientOriginalName(),

            'url' => $this->getUrl($type,$tempFileName),
        ];
    }

    public function getUrl($type,$tempFileName) {

        $methodName = 'getUrl' . Str::studly($type);

        if (method_exists($this,$methodName)) {
            return $this->$methodName($tempFileName);
        }

        throw new \InvalidArgumentException("url for type not defined");
    }


    public function getUrlFoto($tempFileName) {
        return '/imagecache/small/'.$tempFileName;
    }

    public function getUrlAttachment($tempFileName) {
        return '/downloadtemp/'.$tempFileName;
    }
}
