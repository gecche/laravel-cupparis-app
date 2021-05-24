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
trait HasFileFieldsTrait
{



    protected function getStorageFilename($field, $relative = false)
    {

        return $this->{$field};

    }

    public function fileExists($field)
    {
        $filename = $this->getStorageFilename($field);
        return File::exists($filename);
    }

    public function getMimeType($field)
    {
        $filename = $this->getStorageFilename($field);
        return File::mimeType($filename);
    }

    public function deleteFilefields()
    {
        foreach (array_keys($this->fileFields) as $field) {
            $this->deleteFilefield($field);
        }
    }

    public function deleteFilefield($field)
    {
        if ($this->fileExists($field)) {
            File::delete($this->getStorageFilename($field));
        }
    }

    public function delete($id = null)
    {
        $this->deleteFilefields();
        return parent::delete($id);
    }


    public function filesOps($field,$inputArray = array())
    {


            $resource = json_decode(Arr::get($inputArray, $field, ""), true);

            $tempfilename = Arr::get($resource, 'id', false);
            if ($tempfilename && !File::exists($tempfilename)) {

                $fulltempfilename = storage_temp_path($tempfilename);

                $this->deleteFilefield($field);

                $filename = $this->getStorageFilename($field);

                File::move($fulltempfilename, public_path($filename));

            }

    }

    public function setFieldsFromResource($field, $inputArray = array())
    {

        $resource = json_decode(Arr::get($inputArray, $field, ""), true);

        $this->$field = rand() . '_' . Arr::get($resource, 'filename', false);//pathinfo($resourceId, PATHINFO_EXTENSION);

    }

//
//    public static function fullCreate($data = array(), $path = null)
//    {
//        $item = new static;
//        $item->fill($data);
//        $item->save();
//
//        $item->deleteOldFiles();
//        if ($path) {
//            $diskDriver = property_exists($item, 'disk_driver') ? $item->disk_driver : 'local';
//
//            if ($diskDriver == 'local') {
//                File::copy($path, storage_path($item->getStorageFilename()));
//            } else {
//                File::disk($diskDriver)->put($item->getStorageFilename(), File::get($path));
//
//            }
//
//        }
//        return $item;
//    }


    public function getResourceAttribute($field)
    {
        $defaultInfo = [
            'id' => null,
            'url' => 'imagecache/small/0',
            'mimetype' => null,
        ];

        if (!$this->getKey()) {
            return $defaultInfo;
        }

        try {
            $info = [
                'id' => $this->getStorageFilename($field),
                'url' => $this->getStorageFilename($field),
                'mimetype' => $this->getMimeType($field),

            ];
        } catch (\Exception $e) {
            $info = $defaultInfo;
        }
        return $info;
    }

}
