<?php

namespace assets\page;

use assets\data\asset\Asset;
use assets\data\asset\modification\AssetModificationLogList;
use assets\data\asset\modification\ViewableAssetModificationLog;
use wcf\data\comment\StructuredCommentList;
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
     * @var ViewableAssetModificationLog[]
     */
    public $modificationLogs;

    /**
     * list of comments
     * @var StructuredCommentList
     */
    public $commentList;

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
        WCF::getTPL()->assign([
            'object' => $this->object,
            'commendList' => [],//$this->commentList,  TODO
            'modificationLogs' => $this->modificationLogs
        ]);
    }
}
