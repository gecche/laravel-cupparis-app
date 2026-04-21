<?php

namespace Gecche\Cupparis\App\Models\Relations;

trait QueueRelations
{

    public function user() {

        return $this->belongsTo('App\Models\User', 'user_id', null, null);
    
    }



}
