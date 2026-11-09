<?php

namespace Gecche\Cupparis\App\Foorm;

use Gecche\Foorm\FoormQueueManager as BaseFoormManager;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FoormQueueManager extends BaseFoormManager
{
    use FoormManagerTrait;

}
