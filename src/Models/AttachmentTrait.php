<?php

namespace Gecche\Cupparis\App\Models;

use Illuminate\Support\Facades\File;

use Carbon\Carbon;

/**
 * Eloquent model for acl_groups table.
 * This is used by Eloquent permissions provider.
 */
trait AttachmentTrait {
    
//    public function getNameExt() {
//        $ext = $this->ext();
//        $name = $this->nome;
//        $name = str_slug($name);
//        if (Str::endsWith($name, $ext))
//           return $name;
//        return $name . $ext;
//    }


    public function getUrl()
    {
        return 'downloadmediable/attachment/'.$this->getKey();
    }
}
