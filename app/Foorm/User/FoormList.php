<?php

namespace App\Foorm\User;


use Gecche\Cupparis\App\Foorm\Base\FoormList as BaseFoormList;
use Illuminate\Support\Arr;

class FoormList extends BaseFoormList
{


    public function finalizeData($finalizationFunc = null)
    {
        parent::finalizeData($finalizationFunc); // TODO: Change the autogenerated stub

        foreach ($this->formData['data'] as $key => $record) {

            $record['mainrole'] = Arr::get($record['mainrole'],'name',"Nessun ruolo");
            $record['email_verified_at'] = $record['email_verified_at'] ? 1 : 0;

            $this->formData['data'][$key] = $record;
        }

        return;

    }

}