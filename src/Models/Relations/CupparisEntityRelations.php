<?php

namespace Gecche\Cupparis\App\Models\Relations;

trait CupparisEntityRelations
{

    public function fields() {

        return $this->hasMany('App\Models\CupparisEntityField', 'entity_id', null);
    
    }



}
