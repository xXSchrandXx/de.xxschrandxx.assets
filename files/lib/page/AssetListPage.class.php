<?php

namespace assets\page;

use assets\data\asset\AssetList;
use assets\data\category\AssetCategoryNodeTree;
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
    public $itemsPerPage = 100;

    /**
     * @inheritDoc
     */
    public $validSortFields = [
        'assetID',
        'legacyID',
        'title',
        'amount',
        'lastTimeModified',
        'time'
    ];

    /**
     * @inheritDoc
     */
    public $defaultSortField = ASSETS_LEGACYID_ENABLED ? 'assetID' : 'legacyID';

    /**
     * @inheritDoc
     */
    public $forceCanonicalURL = true;

    /**
     * available categories
     * @var array
     */
    public array $availableCategories = [];

    /**
     * available location
     * @var array
     */
    public array $availableLocations = [];

    /**
     * List of available category ids
     * @var array;
     */
    protected $assetCategoryNodeTreeIDs = [];

    /**
     * List of forbidden category ids
     * @var array;
     */
    protected $forbiddenCategoryIDs = [];

    /**
     * List of available location ids
     * @var array;
     */
    protected $assetLocationNodeTreeIDs = [];

    /**
     * List of forbidden location ids
     * @var array;
     */
    protected $forbiddenLocationIDs = [];

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        $parameters = [];

        // TODO

        $this->canonicalURL = LinkHandler::getInstance()->getControllerLink($this::class, $parameters);
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        // can see deleted
        $canSeeDeleted = WCF::getSession()->getPermission('admin.assets.canDelete');
        if (!$canSeeDeleted) {
            $this->objectList->getConditionBuilder()->add('isThrashed != 1');
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
            } else if ($canSeeDeleted && $assetCategory->canDelete()) {
                continue;
            }
            $this->forbiddenCategoryIDs[] = $assetCategory->getObjectID();
        }
        if (!empty($this->forbiddenCategoryIDs)) {
            $this->objectList->getConditionBuilder()->add('categoryID NOT IN (?)', [$this->forbiddenCategoryIDs]);
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
            } else if ($canSeeDeleted && $assetLocation->canDelete()) {
                continue;
            }
            $this->forbiddenLocationIDs[] = $assetLocation->getObjectID();
        }
        if (!empty($this->forbiddenLocationIDs)) {
            $this->objectList->getConditionBuilder()->add('locationID NOT IN (?)', [$this->forbiddenLocationIDs]);
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'assetCategoryNodeTreeIDs' => $this->assetCategoryNodeTreeIDs,
            'forbiddenCategoryIDs' => $this->forbiddenCategoryIDs,
            'assetLocationNodeTreeIDs' => $this->assetLocationNodeTreeIDs,
            'forbiddenLocationIDs' => $this->forbiddenLocationIDs,
            'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.xxschrandxx.assets.asset')),
        ]);
    }
}
