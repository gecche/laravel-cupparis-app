<?php

namespace Gecche\Cupparis\App\Models;

use Carbon\Carbon;
use Gecche\Breeze\Breeze;

/**
 * Eloquent model for acl_groups table.
 * This is used by Eloquent permissions provider.
 */
trait FotoTrait {


    public function getUrl()
    {
        if ($this->fileExists()) {
            return '/viewmediable/foto/'.$this->getKey();
        }
        return '/imagecache/small/0';
    }

    public function getIconaFotoUrl($random = false, $template = null, $default = true) {

        if (!$random) {
            $foto = $this->fotos()->take(1)->get()->first();
        } else {
            $count = $this->fotos()->count() - 1;
            $foto = $this->fotos()->take(1)->skip(rand(0, $count))->get()->first();
        }
        if ($foto) {
            return $foto->url;
        }
        return false;

    }


}
