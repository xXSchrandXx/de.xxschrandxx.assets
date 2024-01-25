<?php

namespace assets\data\option;

use assets\system\cache\builder\AssetOptionCacheBuilder;
use wcf\data\custom\option\CustomOptionEditor;
use wcf\data\IEditableCachedObject;

/**
 * @property    AssetOption   $object
 * @method      AssetOption   getDecoratedObject()
 * @mixin       AssetOption
 */
class AssetOptionEditor extends CustomOptionEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = AssetOption::class;

    /**
     * @inheritDoc
     */
    public static function resetCache()
    {
        AssetOptionCacheBuilder::getInstance()->reset();
    }
}
