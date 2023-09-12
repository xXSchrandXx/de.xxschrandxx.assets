<?php

namespace assets\system\cache\builder;

use assets\data\asset\AssetList;
use assets\data\asset\modification\AssetModificationLogList;
use wcf\data\category\CategoryList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\category\CategoryHandler;

class StatsCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected $maxLifetime = 1200;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters)
    {
        $data = [];

        // assets per day
        $days = \ceil((TIME_NOW - ASSETS_INSTALL_TIME) / 86400);
        if ($days <= 0) {
            $days = 1;
        }

        $categoryHandler = CategoryHandler::getInstance();

        $categoryObjectType = $categoryHandler->getObjectTypeByName('de.xxschrandxx.assets.category');
        $categoryList = new CategoryList();
        $categoryList->getConditionBuilder()->add('objectTypeID = ?', [$categoryObjectType->getObjectID()]);
        $data['categoryCount'] = $categoryList->countObjects();

        $locationObjectType = $categoryHandler->getObjectTypeByName('de.xxschrandxx.assets.location');
        $locationList = new CategoryList();
        $locationList->getConditionBuilder()->add('objectTypeID = ?', [$locationObjectType->getObjectID()]);
        $data['locationCount'] = $locationList->countObjects();

        $assetList = new AssetList();
        $data['assetCount'] = $assetList->countObjects();

        $data['assetsPerDay'] = $data['assetCount'] / $days;

        $modificationLogList = new AssetModificationLogList();
        $modificationLogList->getConditionBuilder()->add('action != ?', ['audit']);
        $data['modificationCount'] = $modificationLogList->countObjects();

        $data['modificationsPerDay'] = $data['modificationCount'] / $days;

        $auditLogList = new AssetModificationLogList();
        $auditLogList->getConditionBuilder()->add('action = ?', ['audit']);
        $data['auditCount'] = $auditLogList->countObjects();

        $data['auditsPerDay'] = $data['auditCount'] / $days;

        return $data;
    }
}
