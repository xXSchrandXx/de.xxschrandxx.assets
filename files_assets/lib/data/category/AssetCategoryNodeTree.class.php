<?php

namespace assets\data\category;

use wcf\data\category\CategoryNodeTree;

class AssetCategoryNodeTree extends CategoryNodeTree
{
    /**
     * @inheritDoc
     */
    protected $nodeClassName = AssetCategoryNode::class;

    /**
     * @inheritDoc
     */
    public function __construct(
        $objectType = 'de.xxschrandxx.assets.category',
        $parentCategoryID = 0,
        $includeDisabledCategories = false,
        array $excludedCategoryIDs = []
    ) {
        parent::__construct(
            $objectType,
            $parentCategoryID,
            $includeDisabledCategories,
            $excludedCategoryIDs
        );
    }
}
