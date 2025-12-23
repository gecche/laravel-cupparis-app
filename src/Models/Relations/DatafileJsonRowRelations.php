<?php

namespace Gecche\Cupparis\App\Models\Relations;


trait DatafileJsonRowRelations
{

    public function fields() {

        return $this->morphTo('datafile');
    
    }



}
