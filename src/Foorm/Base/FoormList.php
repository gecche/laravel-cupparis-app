<?php

namespace Gecche\Cupparis\App\Foorm\Base;

use Gecche\Foorm\FoormList as BaseFoormList;

class FoormList extends BaseFoormList
{
    use CupparisFoormTrait;

    protected function applyListBuilder()
    {
        if ($this->listBuilder instanceof \Closure) {
            $builder = $this->listBuilder;
            $this->formBuilder = $builder($this->model);
            return;
        }

        $modelClass = get_class($this->model);
        $this->formBuilder = $modelClass::acl();

    }
}
