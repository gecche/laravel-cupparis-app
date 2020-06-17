<?php

namespace Gecche\Cupparis\App\Foorm\Base;

use Gecche\Foorm\FoormList as BaseFoormList;
use Illuminate\Support\Arr;

class FoormList extends BaseFoormList
{
    use CupparisFoormTrait;

    protected function applyListBuilder()
    {
        if (Arr::get($this->customFuncs,'listBuilder') instanceof \Closure) {
            $builder = $this->customFuncs['listBuilder'];
            $this->formBuilder = $builder($this->model);
            return;
        }

        $modelClass = get_class($this->model);
        $this->formBuilder = $modelClass::acl();

    }
}
