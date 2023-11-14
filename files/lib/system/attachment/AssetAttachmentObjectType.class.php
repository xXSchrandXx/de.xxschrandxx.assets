<?php

namespace assets\system\attachment;

use assets\data\asset\Asset;
use assets\data\asset\AssetList;
use wcf\system\attachment\AbstractAttachmentObjectType;

/**
 * @var Asset[] $cachedObjects
 * @method Asset getObject($objectID)
 */
class AssetAttachmentObjectType extends AbstractAttachmentObjectType
{
    /**
     * @inheritDoc
     */
    public function cacheObjects(array $objectIDs)
    {
        $objectList = new AssetList();
        $objectList->setObjectIDs($objectIDs);
        $objectList->readObjects();
        $this->cachedObjects = $objectList->getObjects();
    }

    /**
     * @inheritDoc
     */
    public function canDownload($objectID)
    {
        return $this->getObject($objectID)->canView();
    }

    /**
     * @inheritDoc
     */
    public function canUpload($objectID, $parentObjectID = 0)
    {
        return $this->getObject($objectID)->canModify();
    }

    /**
     * @inheritDoc
     */
    public function canDelete($objectID)
    {
        return $this->canUpload($objectID);
    }
}
