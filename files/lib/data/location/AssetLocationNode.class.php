<?php

namespace assets\data\location;

use wcf\data\category\CategoryNode;

/**
 * @method AssetLocation getDecoratedObject()
 */
class AssetLocationNode extends CategoryNode
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = AssetLocation::class;
}
