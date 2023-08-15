<?php

namespace assets\data\asset\modification;

use assets\system\log\modification\AssetModificationLogHandler;
use wcf\data\modification\log\ModificationLogList;

/**
* @method   ViewableAssetModificationLog        current()
* @method   ViewableAssetModificationLog[]      getObjects()
* @method   ViewableAssetModificationLog|null   getSingleObject()
* @method   ViewableAssetModificationLog|null   search($objectID)
* @property ViewableAssetModificationLog[]      $objects
*/
class AssetModificationLogList extends ModificationLogList
{
    /**
     * @inheritDoc
     */
    public $decoratorClassName = ViewableAssetModificationLog::class;

    /**
     * @param int[] $assetIDs
     * @param string $action
     */
    public function __construct(array $assetIDs, $action = '')
    {
        parent::__construct();
        $this->getConditionBuilder()->add(
            "objectTypeID = ?",
            [AssetModificationLogHandler::getInstance()->getObjectType()->objectTypeID]
        );
        $this->getConditionBuilder()->add("objectID IN (?)", [$assetIDs]);
        if (!empty($action)) {
            $this->getConditionBuilder()->add("action = ?", [$action]);
        }
    }
}
