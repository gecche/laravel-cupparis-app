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


    protected $nameField = 'nome';

    public function getDir() {

        if (property_exists($this,'dirPolicy')) {
            $methodName = 'getDirPolicy'.Str::studly($this->dirPolicy);
            if (method_exists($this,$methodName)) {
                return $this->$methodName();
            }
        }

        return $this->dir;
    }

    public function getFullFilenameAttribute() {
        return $this->getStorageFilename(null,null, true);
    }
    public function ext() {
        return '.' . $this->ext;
    }
    public function getResourcePathAttribute() {
        return $this->getStorageFilename(null,null, false);
    }


    public function storageResponse($id = null,$name = null, $headers = [], $disposition = 'attachment') {
        $diskDriver = property_exists($this,'disk_driver') ? $this->disk_driver : 'local';
        $filename = $this->getStorageFilename($id);
        if (is_null($name)) {
            if ($this->{$this->nameField}) {
                $name = $this->{$this->nameField} . $this->ext();
            }
        }
        return Storage::disk($diskDriver)->response($filename,$name,$headers,$disposition);
    }

    public function getStorageFilename($id = null,$diskDriver = null,$relative = false) {

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

    public function fileExists($id = null,$diskDriver = null) {
        $filename = $this->getStorageFilename($id,$diskDriver);
        return Storage::exists($filename);
    }

    public function getMimeType($id = null,$diskDriver = null) {
        $filename = $this->getStorageFilename($id,$diskDriver);
        if (is_null($diskDriver)) {
            $diskDriver = property_exists($this,'disk_driver') ? $this->disk_driver : 'local';
        }
        switch ($diskDriver) {
            case 'local':
                return File::mimeType(storage_path($filename));
            default:
                return Storage::mimeType($filename);
        }
    }

    public function getPrefixFile($id) {
        if ($id === null) {
            $id = $this->getKey();
        }
        return $this->prefix.'_'.$id.'_';
    }

    public function delete($id = NULL) {
        if ($this->fileExists()) {
            Storage::delete($this->getStorageFilename());
        }
        return parent::delete($id);
    }

    public function filesOps($inputArray = array(),$field = 'resource') {
        $diskDriver = property_exists($this,'disk_driver') ? $this->disk_driver : 'local';

        $resource = json_decode(Arr::get($inputArray,$field,""),true);

        $tempfilename = Arr::get($resource,'id',false);
        if ($tempfilename && !Storage::exists($tempfilename)) {

            $fulltempfilename = storage_temp_path($tempfilename);

            $this->deleteOldFiles();

            $filename = $this->getStorageFilename(null,$diskDriver);

            if ($diskDriver == 'local') {
                File::ensureDirectoryExists(File::dirname(storage_path($filename)),0755,true);
                File::move($fulltempfilename,storage_path($filename));
                return;
            }

            Storage::disk($diskDriver)->put($filename,File::get($fulltempfilename));


        }

    }

    public function setFieldsFromResource($inputArray = array(),$field = 'resource') {

        $resource = json_decode(Arr::get($inputArray,$field,""),true);

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
            $diskDriver = property_exists($item,'disk_driver') ? $item->disk_driver : 'local';

            if ($diskDriver == 'local') {
                File::copy($path,storage_path($item->getStorageFilename()));
            } else {
                Storage::disk($diskDriver)->put($item->getStorageFilename(),File::get($path));

            }

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
            'url' => '/imagecache/small/0',
            'mimetype' => null
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
