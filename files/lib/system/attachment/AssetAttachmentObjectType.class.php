<?php

namespace assets\system\attachment;

use wcf\system\attachment\AbstractAttachmentObjectType;

class AssetAttachmentObjectType extends AbstractAttachmentObjectType
{
    /**
     * @inheritDoc
     */
    public function canDownload($objectID)
    {
        // TODO
        return true;
    }

    /**
     * @inheritDoc
     */
    public function canViewPreview($objectID)
    {
        return $this->canDownload($objectID);
    }

    /**
     * @inheritDoc
     */
    public function canUpload($objectID, $parentObjectID = 0)
    {
        // TODO
        return true;
    }

    /**
     * @inheritDoc
     */
    public function canDelete($objectID)
    {
        return $this->canUpload($objectID);
    }
}
