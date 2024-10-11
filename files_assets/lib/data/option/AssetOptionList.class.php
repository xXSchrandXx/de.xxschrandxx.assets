<?php

namespace assets\data\option;

use wcf\data\custom\option\CustomOptionList;

/**
 * @property    AssetOption[]     $objects
 * @method      AssetOption[]     getObjects()
 * @method      AssetOption|null  getSingleObject()
 * @method      AssetOption|null  current()
 */
class AssetOptionList extends CustomOptionList
{
    /**
     * @inheritDoc
     */
    public $className = AssetOption::class;
}
