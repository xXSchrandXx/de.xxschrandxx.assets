<?php

namespace assets\data\location;

use wcf\data\category\UncachedCategoryNodeTree;

class AssetLocationNodeTree extends UncachedCategoryNodeTree
{
    /**
     * @inheritDoc
     */
    protected $nodeClassName = AssetLocationNode::class;

    /**
     * @inheritDoc
     */
    public function __construct(
        $objectType = 'de.xxschrandxx.assets.location',
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
