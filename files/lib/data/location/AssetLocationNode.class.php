<?php

namespace assets\data\location;

use assets\data\asset\AssetList;
use assets\data\category\AssetCategory;
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
            $locationIDs = [];
            array_push($locationIDs, $this->getDecoratedObject()->getObjectID());
            /** @var AssetLocation $location */
            foreach ($this->getDecoratedObject()->getAllChildCategories() as $location) {
                array_push($locationIDs, $location->getObjectID());
            }
            $assetList->getConditionBuilder()->add('locationID IN (?)', [$locationIDs]);
            $this->itemCount = $assetList->countObjects();
        }
        return $this->itemCount;
    }
}
