<?php

namespace Gecche\Cupparis\App\Foorm\Base;

use Gecche\Foorm\FoormReport as BaseFoormReport;
use Illuminate\Support\Arr;

class FoormReport extends BaseFoormReport
{
    use CupparisFoormTrait;

//    protected function applyReportBuilder()
//    {
//        if (Arr::get($this->customFuncs,'listBuilder') instanceof \Closure) {
//            $builder = $this->customFuncs['listBuilder'];
//            $this->formBuilder = $builder($this->model);
//            return;
//        }
//
//        $modelClass = get_class($this->model);
//        $this->formBuilder = $modelClass::acl();
//
//    }
}
