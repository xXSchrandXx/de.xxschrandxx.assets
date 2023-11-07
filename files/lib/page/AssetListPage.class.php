<?php

namespace assets\page;

use assets\data\asset\AssetList;
use assets\data\category\AssetCategory;
use assets\data\category\AssetCategoryNodeTree;
use assets\data\location\AssetLocation;
use assets\data\location\AssetLocationNodeTree;
use wcf\page\SortablePage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

class AssetListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'de.xxschrandxx.assets.AssetList';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.assets.canView'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = AssetList::class;

    /**
     * @inheritDoc
     */
    public $validSortFields = [
        'assetID',
        'legacyID',
        'title',
        'amount',
        'nextAudit',
        'lastAudit',
        'lastModification',
        'time'
    ];

    /**
     * @inheritDoc
     */
    public $defaultSortField = ASSETS_LEGACYID_ENABLED ? 'legacyID' : 'assetID';

    /**
     * @inheritDoc
     */
    public $forceCanonicalURL = true;

    /**
     * parameter list for canonical url
     * @var array
     */
    public $canonicalURLParameters = [];

    /**
     * Active category
     * @var AssetCategory
     */
    public $category;

    /**
     * available categories
     * @var array
     */
    public array $availableCategories = [];

    /**
     * List of available category ids
     * @var array
     */
    public $assetCategoryNodeTreeIDs = [];

    /**
     * List of forbidden category ids
     * @var array
     */
    public $forbiddenCategoryIDs = [];

    /**
     * Active location
     * @var AssetLocation
     */
    public $location;

    /**
     * available location
     * @var array
     */
    public array $availableLocations = [];

    /**
     * List of available location ids
     * @var array
     */
    public $assetLocationNodeTreeIDs = [];

    /**
     * List of forbidden location ids
     * @var array
     */
    public $forbiddenLocationIDs = [];

    /**
     * intval to show both
     */
    const FILTER_TRASH_BOTH = 0;

    /**
     * intval to show only trashed
     */
    const FILTER_TRASH_Y = 1;

    /**
     * intval to don't show trashed
     */
    const FILTER_TRASH_N = 2;

    /**
     * active trash filter
     */
    public $filterTrash = 0;

    /**
     * weather the user can see trashed assets
     */
    public $canSeeTrashed = false;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        // read category
        $categoryID = 0;
        if (isset($_REQUEST['categoryID'])) {
            $categoryID = \intval($_REQUEST['categoryID']);
        }
        $this->category = AssetCategory::getCategory($categoryID);
        if (isset($this->category)) {
            $this->canonicalURLParameters['categoryID'] = $this->category->getObjectID();
        }

        // read location
        $locationID = 0;
        if (isset($_REQUEST['locationID'])) {
            $locationID = \intval($_REQUEST['locationID']);
        }
        $this->location = AssetLocation::getCategory($locationID);
        if (isset($this->location)) {
            $this->canonicalURLParameters['locationID'] = $this->location->getObjectID();
        }

        // filter trashed
        $this->canSeeTrashed = WCF::getSession()->getPermission('admin.assets.canDelete');

        if (isset($_REQUEST['trash'])) {
            $tmpFilterTrash = \intval($_REQUEST['trash']);
            if (
                $tmpFilterTrash == self::FILTER_TRASH_BOTH && $this->canSeeTrashed ||
                $tmpFilterTrash == self::FILTER_TRASH_Y && $this->canSeeTrashed ||
                $tmpFilterTrash == self::FILTER_TRASH_N
            ) {
                $this->filterTrash = $tmpFilterTrash;
            }
        }
        $this->canonicalURLParameters['trash'] = $this->filterTrash;

        $this->canonicalURL = LinkHandler::getInstance()->getControllerLink($this::class, $this->canonicalURLParameters);
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        if ($this->canSeeTrashed) {
            switch ($this->filterTrash) {
                case self::FILTER_TRASH_Y:
                    $this->objectList->getConditionBuilder()->add('isTrashed = 1');
                    break;
                case self::FILTER_TRASH_N:
                    $this->objectList->getConditionBuilder()->add('isTrashed != 1');
                    break;
            }
        } else {
            $this->objectList->getConditionBuilder()->add('isTrashed != 1');
        }

        // get forbidden categories
        $assetCategoryNodeTree = new AssetCategoryNodeTree();
        foreach ($assetCategoryNodeTree->getIterator() as $assetCategoryNode) {
            /** @var \assets\data\category\AssetCategoryNode $assetCategoryNode */
            /** @var \assets\data\category\AssetCategory */
            $assetCategory = $assetCategoryNode->getDecoratedObject();
            $this->assetCategoryNodeTreeIDs[] = $assetCategory->getObjectID();
            if ($assetCategory->canView()) {
                continue;
            } else if ($this->canSeeTrashed && $assetCategory->canDelete()) {
                continue;
            }
            $this->forbiddenCategoryIDs[] = $assetCategory->getObjectID();
        }
        if (!empty($this->forbiddenCategoryIDs)) {
            $this->objectList->getConditionBuilder()->add('categoryID NOT IN (?)', [$this->forbiddenCategoryIDs]);
        }
        if ($this->category !== null && !in_array($this->category->getObjectID(), $this->forbiddenCategoryIDs)) {
            $categoryIDs = [];
            array_push($categoryIDs, $this->category->getObjectID());
            /** @var AssetCategory $category */
            foreach ($this->category->getAllChildCategories() as $category) {
                if (in_array($category->getObjectID(), $this->forbiddenCategoryIDs)) {
                    continue;
                }
                array_push($categoryIDs, $category->getObjectID());
            }
            $this->objectList->getConditionBuilder()->add('categoryID IN (?)', [$categoryIDs]);
        }

        // get forbidden locations
        $assetLocationNodeTree = new AssetLocationNodeTree();
        foreach ($assetLocationNodeTree->getIterator() as $assetLocationNode) {
            /** @var \assets\data\location\AssetLocationNode $assetLocationNode */
            /** @var \assets\data\location\AssetLocation */
            $assetLocation = $assetLocationNode->getDecoratedObject();
            $this->assetLocationNodeTreeIDs[] = $assetLocation->getObjectID();
            if ($assetLocation->canView()) {
                continue;
            } else if ($this->canSeeTrashed && $assetLocation->canDelete()) {
                continue;
            }
            $this->forbiddenLocationIDs[] = $assetLocation->getObjectID();
        }
        if (!empty($this->forbiddenLocationIDs)) {
            $this->objectList->getConditionBuilder()->add('locationID NOT IN (?)', [$this->forbiddenLocationIDs]);
        }
        if (isset($this->location) && !in_array($this->location->getObjectID(), $this->forbiddenLocationIDs)) {
            $locatioNIDs = [];
            array_push($locatioNIDs, $this->location->getObjectID());
            /** @var AssetLocation $location */
            foreach ($this->location->getAllChildCategories() as $location) {
                if (in_array($location->getObjectID(), $this->forbiddenLocationIDs)) {
                    continue;
                }
                array_push($locatioNIDs, $location->getObjectID());
            }
            $this->objectList->getConditionBuilder()->add('locationID IN (?)', [$locatioNIDs]);
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'categoryID' => isset($this->category) ? $this->category->getObjectID() : 0,
            'categoryName' => isset($this->category) ? $this->category->getTitle() : null,
            'locationID' => isset($this->location) ? $this->location->getObjectID() : 0,
            'locationName' => isset($this->location) ? $this->location->getTitle() : null,
            'trash' => $this->filterTrash,
            'assetCategoryNodeTreeIDs' => $this->assetCategoryNodeTreeIDs,
            'assetLocationNodeTreeIDs' => $this->assetLocationNodeTreeIDs,
            'canSeeTrashed' => $this->canSeeTrashed,
            'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.xxschrandxx.assets.asset'))
        ]);
    }
}
