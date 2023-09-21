<?php

namespace assets\system\bulk\processing\actions;

use assets\data\asset\AssetAction;
use assets\data\asset\AssetList;
use InvalidArgumentException;
use wcf\data\DatabaseObjectList;
use wcf\system\bulk\processing\AbstractBulkProcessingAction;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\WCF;

abstract class AbstractAssetBulkProcessingAction extends AbstractBulkProcessingAction
{
    /**
     * Name of executed action
     */
    public $actionName = '';

    /**
     * AssetAction set after executeAction
     * @var AssetAction
     */
    protected $assetAction;

    /**
     * @inheritDoc
     */
    public function executeAction(DatabaseObjectList $objectList)
    {
        if (empty($this->actionName)) {
            throw new InvalidArgumentException('Unknown action name');
        }
        if (!($objectList instanceof AssetList)) {
            throw new InvalidObjectArgument($objectList, AssetList::class, 'Object list');
        }

        $objectList->readObjects();
        $assets = $objectList->getObjects();

        if (empty($assets)) {
            return;
        }
        $this->assetAction = new AssetAction(
            $assets,
            $this->actionName, 
            [
                'data' => [
                    $this->actionName == 'audit' ? 'comment' : 'reason' => WCF::getLanguage()->get('assets.acp.asset.bulkProcessing.log')
                ]
            ]
        );
        $this->assetAction->executeAction();
    }

    /**
     * @inheritDoc
     */
    public function getObjectList()
    {
        return new AssetList();
    }
}
