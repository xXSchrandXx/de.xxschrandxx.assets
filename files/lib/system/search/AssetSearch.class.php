<?php

namespace assets\system\search;

use assets\data\asset\Asset;
use assets\data\asset\AssetList;
use assets\data\category\AssetCategory;
use assets\data\category\AssetCategoryNodeTree;
use assets\data\location\AssetLocation;
use assets\data\location\AssetLocationNodeTree;
use wcf\data\search\ISearchResultObject;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\search\AbstractSearchProvider;
use wcf\system\WCF;

class AssetSearch extends AbstractSearchProvider
{
    /**
     * @var int
     */
    private $categoryID = 0;

    /**
     * @var int
     */
    private $locationID = 0;

    /**
     * @var Asset[]
     */
    private $cache = [];

    /**
     * @inheritDoc
     */
    public function cacheObjects(array $objectIDs, ?array $additionalData = null): void
    {
        $list = new AssetList();
        $list->setObjectIDs($objectIDs);
        $list->readObjects();
        /** @var Asset $asset */
        foreach ($list->getObjects() as $asset) {
            $this->cache[$asset->getObjectID()] = $asset;
        }
    }

    /**
     * @inheritDoc
     */
    public function getObject(int $objectID): ?ISearchResultObject
    {
        return $this->cache[$objectID] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getTableName(): string
    {
        return 'assets' . WCF_N . '_asset';
    }

    /**
     * @inheritDoc
     */
    public function getIDFieldName(): string
    {
        return $this->getTableName() . '.assetID';
    }

    /**
     * @inheritDoc
     */
    public function getSubjectFieldName(): string
    {
        return $this->getTableName() . '.title';
    }

    
    /**
     * @inheritDoc
     */
    public function getUsernameFieldName(): string
    {
        return "''";
    }

    /**
     * @inheritDoc
     */
    public function getTimeFieldName(): string
    {
        return $this->getTableName() . '.time';
    }

    /**
     * @inheritDoc
     */
    public function getConditionBuilder(array $parameters): ?PreparedStatementConditionBuilder
    {
        $conditionBuilder = new PreparedStatementConditionBuilder();
        if (!empty($parameters['assetCategoryID'])) {
            $this->categoryID = \intval($parameters['assetCategoryID']);
        }
        if ($this->categoryID) {
            /** @var AssetCategory[] */
            $categories = $this->getCategories($this->categoryID);
            /** @var AssetCategory[] */
            $viewableCategoryIDs = [];
            foreach ($categories as $category) {
                if (!$category->canView()) {
                    continue;
                }
                $viewableCategoryIDs[] = $category->getObjectID();
            }
            if (empty($viewableCategoryIDs)) {
                $conditionBuilder->add('1=0');
            } else {
                $conditionBuilder->add($this->getTableName() . '.categoryID IN (?)', [$viewableCategoryIDs]);
            }
        }

        if (!empty($parameters['assetLocationID'])) {
            $this->locationID = \intval($parameters['assetLocationID']);
        }
        if ($this->locationID) {
            /** @var AssetLocation[] */
            $locations = $this->getLocations($this->locationID);
            /** @var AssetLocation[] */
            $viewableLocationIDs = [];
            foreach ($locations as $location) {
                if (!$location->canView()) {
                    continue;
                }
                $viewableLocationIDs[] = $location->getObjectID();
            }
            if (empty($viewableLocationIDs)) {
                $conditionBuilder->add('1=0');
            } else {
                $conditionBuilder->add($this->getTableName() . '.categoryID IN (?)', [$viewableLocationIDs]);
            }
        }

        $conditionBuilder->add($this->getTableName() . '.isTrashed = 0');

        return $conditionBuilder;
    }

    private function getCategories(int $categoryID): array
    {
        $categories = [];

        if ($categoryID) {
            if (($category = AssetCategory::getCategory($categoryID)) !== null) {
                $categories[] = $category;
                foreach ($category->getAllChildCategories() as $childCategory) {
                    $categories[] = $childCategory;
                }
            }
        }

        return $categories;
    }

    private function getLocations(int $locationID): array
    {
        $locations = [];

        if ($locationID) {
            if (($location = AssetLocation::getCategory($locationID)) !== null) {
                $locations[] = $location;
                foreach ($location->getAllChildCategories() as $childCategory) {
                    $locations[] = $childCategory;
                }
            }
        }

        return $locations;
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalData(): ?array
    {
        return [
            'assetCategoryID' => $this->categoryID,
            'assetLocationID' => $this->locationID
        ];
    }

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        WCF::getTPL()->assign([
            'assetCategoryList' => (new AssetCategoryNodeTree())->getIterator(),
            'assetLocationList' => (new AssetLocationNodeTree())->getIterator()
        ]);
    }

    
    /**
     * @inheritDoc
     */
    public function getFormTemplateName(): string
    {
        return 'searchAsset';
    }
}
