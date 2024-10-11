<?php

namespace assets\system\cache\builder;

use assets\data\option\AssetOptionList;
use wcf\system\cache\builder\AbstractCacheBuilder;

class AssetOptionCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters)
    {
        $optionList = new AssetOptionList();
        $optionList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $optionList->sqlOrderBy = 'showOrder ASC';
        $optionList->readObjects();

        return $optionList->getObjects();
    }
}
