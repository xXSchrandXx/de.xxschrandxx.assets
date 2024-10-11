<?php

namespace assets\system\attachment;

use assets\data\asset\Asset;
use assets\data\asset\AssetList;
use wcf\system\attachment\AbstractAttachmentObjectType;
use wcf\system\WCF;

/**
 * @var Asset[] $cachedObjects
 * @method ?Asset getObject($objectID)
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
        $object = $this->getObject($objectID);
        if (isset($object)) {
            return $object->canView();
        }
        return WCF::getSession()->getPermission('user.assets.canView');
    }

    /**
     * @inheritDoc
     */
    public function canUpload($objectID, $parentObjectID = 0)
    {
        $object = $this->getObject($objectID);
        if (isset($object)) {
            return $object->canAdd();
        }
        return WCF::getSession()->getPermission('mod.assets.canAdd');
    }

    /**
     * @inheritDoc
     */
    public function canDelete($objectID)
    {
        $object = $this->getObject($objectID);
        if (isset($object)) {
            return $object->canDelete();
        }
        return WCF::getSession()->getPermission('admin.assets.canDelete');
    }
}
