<?php

namespace Gecche\Cupparis\App\Models;

use Carbon\Carbon;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

/**
 * Eloquent model for acl_groups table.
 * This is used by Eloquent permissions provider.
 */
trait UploadableTraits {
   
    protected $dirPolicy = null;

    public function getDir() {

        $methodName = 'getDirPolicy'.Str::studly($this->dirPolicy);
        if ($this->dirPolicy && method_exists($this,$methodName)) {
            return $this->$methodName();
        }

        return $this->dir;
    }

    public function getFullFilenameAttribute() {
        return $this->getStorageFilename(null,null, true);
    }
    public function ext() {
        return '.' . $this->ext;
    }


    public function storageResponse($id = null,$name = null, $headers = [], $disposition = 'attachment') {
        $diskDriver = property_exists($this,'disk_driver') ? $this->disk_driver : 'local';
        $filename = $this->getStorageFilename($id);
        return Storage::disk($diskDriver)->response($filename,$name,$headers,$disposition);
    }

    protected function getStorageFilename($id = null,$diskDriver = null,$relative = false) {

        $dt = new Carbon($this->created_at);
        $time = $dt->timestamp;
        $relativePath = $this->getPrefixFile($id).$time.$this->ext();
        if ($relative) {
            return $relativePath;
        }
        if (is_null($diskDriver)) {
            $diskDriver = property_exists($this,'disk_driver') ? $this->disk_driver : 'local';
        }
        //$diskRootDir = Config::get('filesystems.disks.'.$diskDriver.'.relative_root','');

        return 'files/'.$this->getDir().'/'.$relativePath;

    }

    public function fileExists() {
        $filename = $this->getStorageFilename();
        return Storage::exists($filename);
    }

    public function getMimeType() {
        $filename = $this->getStorageFilename();
        return Storage::mimeType($filename);
    }

    public function getPrefixFile($id) {
        if ($id === null) {
            $id = $this->getKey();
        }
        return $this->prefix.'_'.$id.'_';
    }
   
    public function delete($id = NULL) {
        if (file_exists($this->getStorageFilename()))
            unlink($this->getStorageFilename());
        return parent::delete($id);
    }

    public function filesOps($inputArray = array()) {
        $diskDriver = property_exists($this,'disk_driver') ? $this->disk_driver : 'local';

        $resource = json_decode(Arr::get($inputArray,'resource',""),true);

        $tempfilename = Arr::get($resource,'id',false);
        if ($tempfilename && !Storage::exists($tempfilename)) {

            $fulltempfilename = storage_temp_path($tempfilename);

            $this->deleteOldFiles();

            $filename = $this->getStorageFilename(null,$diskDriver);

            if ($diskDriver == 'local') {
                File::move($fulltempfilename,storage_path($filename));
                return;
            }

            Storage::disk($diskDriver)->put($filename,File::get($fulltempfilename));


        }
        
    }

    public function setFieldsFromResource($inputArray = array()) {

        $resource = json_decode(Arr::get($inputArray,'resource',""),true);

        $resourceId = Arr::get($resource,'id',false);

        $this->ext = pathinfo($resourceId, PATHINFO_EXTENSION);

    }

    public function deleteOldFiles($id = null) {
        File::delete(File::glob(storage_path('files/'.$this->getDir()).'/'.$this->getPrefixFile($id).'*'));
    }
    
    public static function fullCreate($data = array(), $path = null) {
        $item = new static;
        $item->fill($data);
        $item->save();

        $item->deleteOldFiles();
        if ($path) {
            File::copy($path, $item->getStorageFilename());
        }
        return $item;
    }


    public function getNameExt($locale = 'it',$fieldName = 'nome') {
        $ext = $this->ext();

        $fieldName = property_exists($this,$fieldName.'_'.$locale) ? $fieldName.'_'.$locale : $fieldName;
        $name = Str::slug($this->$fieldName);
        if (Str::endsWith($name, $ext))
            return $name;
        return $name . $ext;
    }

    public function getResourceAttribute() {
        $defaultInfo = [
            'id' => null,
            'url' => 'imagecache/small/0',
            'mimetype' => null,
        ];

        if (!$this->getKey()) {
            return $defaultInfo;
        }

        try {
            $info =  [
                'id' => $this->getStorageFilename(),
                'url' => $this->getUrl(),
                'mimetype' => $this->getMimeType(),

            ];
        } catch (\Exception $e) {
              $info = $defaultInfo;
        }
        return $info;
    }
}