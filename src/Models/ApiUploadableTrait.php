<?php

namespace Gecche\Cupparis\App\Models;

use Illuminate\Support\Facades\Storage;

trait ApiUploadableTrait {

    public function getApiUrlAttribute() {
        return 'api/'.$this->getUrl();
    }

    public function storageResponseApi($id = null,$name = null) {
        $diskDriver = property_exists($this,'disk_driver') ? $this->disk_driver : 'local';
        $filename = $this->getStorageFilename($id);
        if (is_null($name)) {
            if ($this->{$this->nameField}) {
                $name = $this->{$this->nameField} . $this->pointedExt();
            }
        }
        $fileContent = Storage::disk($diskDriver)->get($filename);
        return [
            'content' => base64_encode($fileContent),
            'mime' => Storage::mimeType($filename),
            'name' => $name,
        ];
    }

}
