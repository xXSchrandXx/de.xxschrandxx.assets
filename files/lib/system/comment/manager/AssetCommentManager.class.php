<?php

namespace assets\system\comment\manager;

use assets\data\asset\Asset;
use assets\data\asset\AssetEditor;
use DateTime;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\WCF;

class AssetCommentManager extends AbstractCommentManager
{
    /**
     * @inheritDoc
     */
    public $commentsPerPage = 30;

    /**
     * @inheritDoc
     */
    protected $permissionAdd = 'user.assets.canComment';

    /**
     * @inheritDoc
     */
    protected $permissionAddWithoutModeration = 'mod.assets.canCommentWithoutModeration';

    /**
     * @inheritDoc
     */
    protected $permissionCanModerate = 'mod.assets.canModerateComments';

    /**
     * @inheritDoc
     */
    protected $permissionDelete = 'user.assets.canComment';

    /**
     * @inheritDoc
     */
    protected $permissionEdit = 'user.assets.canComment';

    /**
     * @inheritDoc
     */
    protected $permissionModDelete = 'mod.assets.canModerateComments';

    /**
     * @inheritDoc
     */
    protected $permissionModEdit = 'mod.assets.canModerateComments';

    /**
     * @inheritDoc
     */
    protected $permissionDisallowedBBCodes = 'user.comment.disallowedBBCodes';

    /**
     * @inheritDoc
     */
    public function isAccessible($objectID, $validateWritePermission = false)
    {
        $asset = new Asset($objectID);
        if ($asset === null || !$asset->canView()) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function canAdd($objectID)
    {
        $asset = new Asset($objectID);
        if ($asset === null || $asset->isTrashed()) {
            return false;
        }

        return parent::canAdd($objectID);
    }

    /**
     * @inheritDoc
     */
    public function getLink($objectTypeID, $objectID)
    {
        $asset = new Asset($objectID);
        if ($asset === null) {
            return '';
        }

        return $asset->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getTitle($objectTypeID, $objectID, $isResponse = false)
    {
        if ($isResponse) {
            return WCF::getLanguage()->get('assets.page.asset.comment.response');
        }

        return WCF::getLanguage()->getDynamicVariable('assets.page.asset.comment');
    }

    /**
     * @inheritDoc
     */
    public function updateCounter($objectID, $value)
    {
        $editor = new AssetEditor(new Asset($objectID));
        $editor->updateCounters([
            'comments' => $value
        ]);
        $editor->update([
            'lastComment' => (new DateTime())->format(DateTime::ATOM)
        ]);
    }
}
