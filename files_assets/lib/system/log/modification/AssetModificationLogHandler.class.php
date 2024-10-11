<?php

namespace assets\system\log\modification;

use assets\data\asset\Asset;
use assets\data\asset\modification\ViewableAssetModificationLog;
use wcf\data\modification\log\ModificationLog;
use wcf\system\log\modification\AbstractExtendedModificationLogHandler;

class AssetModificationLogHandler extends AbstractExtendedModificationLogHandler
{
    /**
     * @inheritDoc
     */
    protected $objectTypeName = 'de.xxschrandxx.assets.asset';

    /**
     * @inheritDoc
     */
    public function getAvailableActions()
    {
        return ['delete', 'edit', 'restore', 'trash', 'audit'];
    }

    /**
     * @inheritDoc
     */
    public function processItems(array $items)
    {
        /** @var ModificationLog $item */
        foreach ($items as &$item) {
            $assetID = $item->objectID;
            if (!$assetID) {
                unset($item);
                continue;
            }
            $asset = new Asset($assetID);
            if ($assetID != $asset->getObjectID()) {
                unset($item);
                continue;
            }
            $item = new ViewableAssetModificationLog($item, $asset);
        }
        return $items;
    }

    /**
     * @param Asset $asset
     */
    public function delete(Asset $asset)
    {
        $this->add($asset, 'delete', [
            'time' => $asset->time,
            'username' => $asset->username,
        ]);
    }

    /**
     * @param Asset $asset
     * @param string $reason
     */
    public function edit(Asset $asset, $reason = '')
    {
        $this->add($asset, 'edit', ['reason' => $reason]);
    }

    /**
     * @param Asset $asset
     * @param string $reason
     */
    public function restore(Asset $asset, $reason = '')
    {
        $this->add($asset, 'restore', ['reason' => $reason]);
    }

    /**
     * @param Asset $asset
     * @param string $reason
     */
    public function trash(Asset $asset, $reason = '')
    {
        $this->add($asset, 'trash', ['reason' => $reason]);
    }

    /**
     * @param Asset $asset
     * @param string $comment
     */
    public function audit(Asset $asset, $comment = '')
    {
        $this->add($asset, 'audit', ['comment' => $comment]);
    }

    /**
     * @param Asset $asset
     * @param string $action
     * @param array $additionalData
     */
    public function add(Asset $asset, $action, array $additionalData = [])
    {
        $this->createLog($action, $asset->assetID, null, $additionalData);
    }
}
