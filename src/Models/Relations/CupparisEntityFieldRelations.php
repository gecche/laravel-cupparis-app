<?php

namespace Gecche\Cupparis\App\Models\Relations;

trait CupparisEntityFieldRelations
{

    public function entity() {

        return $this->belongsTo('App\Models\CupparisEntity', 'entity_id', null, null);
    
    }



}
