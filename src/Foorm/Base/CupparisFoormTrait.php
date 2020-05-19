<?php

namespace Gecche\Cupparis\App\Foorm\Base;

trait CupparisFoormTrait
{

    protected function getForSelectList($relationName,$relationModel) {
        return $relationModel->getForSelectList($relationModel::acl(), null, [], null, null);
    }


}
