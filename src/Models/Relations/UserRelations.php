<?php

namespace Gecche\Cupparis\App\Models\Relations;

trait UserRelations
{

    public function fotos() {

        return $this->morphMany('App\Models\Foto', 'mediable', null, null, null)
            ->orderBy('ordine','ASC');


    }

    public function attachments() {

        return $this->morphMany('App\Models\Attachment', 'mediable', null, null, null)
            ->orderBy('ordine','ASC');

    }

}
