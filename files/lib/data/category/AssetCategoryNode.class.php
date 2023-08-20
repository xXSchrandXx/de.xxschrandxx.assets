<?php

namespace assets\data\category;

use assets\data\asset\AssetList;
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

    /**
     * @var int
     */
    protected $itemCount = 0;

    /**
     * @inheritDoc
     */
    public function getItems(): int
    {
        if ($this->itemCount == 0) {
            $assetList = new AssetList();
            $categoryIDs = [];
            array_push($categoryIDs, $this->getDecoratedObject()->getObjectID());
            /** @var AssetCategory $category */
            foreach ($this->getDecoratedObject()->getAllChildCategories() as $category) {
                array_push($categoryIDs, $category->getObjectID());
            }
            $assetList->getConditionBuilder()->add('categoryID IN (?)', [$categoryIDs]);
            $this->itemCount = $assetList->countObjects();
        }
        return $this->itemCount;
    }
}
