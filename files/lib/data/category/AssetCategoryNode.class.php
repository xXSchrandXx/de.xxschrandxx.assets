<?php

namespace assets\data\category;

use wcf\data\category\CategoryNode;

/**
 * @method AssetCategory getDecoratedObject()
 */
class AssetCategoryNode extends CategoryNode
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = AssetCategory::class;
}
