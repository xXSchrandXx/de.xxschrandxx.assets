<?php

namespace assets\page;

use assets\data\asset\Asset;
use assets\data\asset\modification\AssetModificationLogList;
use assets\data\asset\modification\ViewableAssetModificationLog;
use assets\system\comment\manager\AssetCommentManager;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

class AssetPage extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.assets.canView'];

    /**
     * @var Asset
     */
    public $object;

    /**
     * @var string
     */
    public $highlightTitle = false;

    /**
     * @var ViewableAssetModificationLog[]
     */
    public $modificationLogs;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['id']) && \is_numeric($_REQUEST['id'])) {
            $this->object = new Asset((int)$_REQUEST['id']);
        }

        if (!$this->object->getObjectID()) {
            throw new IllegalLinkException();
        }

        if (isset($_REQUEST['highlight']) && $_REQUEST['highlight'] == $this->object->getTitle()) {
            $this->highlightTitle = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function checkPermissions()
    {
        if (!$this->object->canView()) {
            throw new PermissionDeniedException();
        }

        parent::checkPermissions();
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        $assetModificationLogList = new AssetModificationLogList([$this->object->getObjectID()]);
        $assetModificationLogList->readObjects();
        $this->modificationLogs = $assetModificationLogList->getObjects();
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        $assetCommentManager = AssetCommentManager::getInstance();
        $commentCanAdd = $assetCommentManager->canAdd($this->object->getObjectID());
        WCF::getTPL()->assign([
            'object' => $this->object,
            'commentCanAdd' => $commentCanAdd,
            'commentManager' => $assetCommentManager,
            'commentObjectTypeID' => $this->object->getCommentObjectTypeID(),
            'commentList' => $this->object->getReadCommentList(),
            'lastCommentTime' => $this->object->lastCommentTime,
            'commentObjectID' => $this->object->getObjectID(),
            'commentContainerID' => 'assetComments',
            'modificationLogs' => $this->modificationLogs,
            'highlightTitle' => $this->highlightTitle
        ]);
    }
}
