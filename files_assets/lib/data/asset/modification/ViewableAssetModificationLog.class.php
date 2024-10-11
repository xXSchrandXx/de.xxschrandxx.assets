<?php

namespace assets\data\asset\modification;

use assets\data\asset\Asset;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\modification\log\IViewableModificationLog;
use wcf\data\modification\log\ModificationLog;
use wcf\system\moderation\queue\ModerationQueueManager;

/**
 * @method  ModificationLog     getDecoratedObject()
 * @mixin   ModificationLog
 */
class ViewableAssetModificationLog extends DatabaseObjectDecorator implements IViewableModificationLog
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = ModificationLog::class;

    /**
     * affected object
     * @var Asset
     */
    protected $affectedObject;

    /**
     * Sets link for viewing/editing.
     *
     * @param Asset $object
     */
    public function setAffectedObject(Asset $object)
    {
        $this->affectedObject = $object;
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return ModerationQueueManager::getInstance()->getLink($this->objectTypeID, $this->queueID);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->affectedObject === null ? '' : $this->affectedObject->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getAffectedObject()
    {
        return $this->affectedObject;
    }
}
