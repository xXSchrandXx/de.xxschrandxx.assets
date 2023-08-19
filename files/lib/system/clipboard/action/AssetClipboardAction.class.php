<?php

namespace assets\system\clipboard\action;

use assets\data\asset\AssetAction;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\WCF;

class AssetClipboardAction extends AbstractClipboardAction
{
    /**
     * @inheritDoc
     */
    protected $actionClassActions = ['trash', 'restore', 'delete'];

    /**
     * @inheritDoc
     */
    protected $supportedActions = ['trash', 'restore', 'delete'];

    /**
     * @inheritDoc
     */
    protected $reloadPageOnSuccess = ['trash', 'restore', 'delete'];

    /**
     * @inheritDoc
     */
    public function getClassName()
    {
        return AssetAction::class;
    }

    /**
     * @inheritDoc
     */
    public function getTypeName()
    {
        return 'de.xxschrandxx.assets.asset';
    }

    /**
     * @inheritDoc
     */
    public function execute(array $objects, ClipboardAction $action)
    {
        $item = parent::execute($objects, $action);

        if ($item === null) {
            return;
        }

        // handle actions
        switch ($action->actionName) {
            case 'trash':
                $item->addInternalData(
                    'confirmMessage',
                    WCF::getLanguage()->getDynamicVariable(
                        'wcf.clipboard.item.de.xxschrandxx.assets.asset.trash.confirmMessage',
                        [
                            'count' => $item->getCount(),
                        ]
                    )
                );
                break;
            case 'restore':
                $item->addInternalData(
                    'confirmMessage',
                    WCF::getLanguage()->getDynamicVariable(
                        'wcf.clipboard.item.de.xxschrandxx.assets.asset.restore.confirmMessage',
                        [
                            'count' => $item->getCount(),
                        ]
                    )
                );
                break;
            case 'delete':
                $item->addInternalData(
                    'confirmMessage',
                    WCF::getLanguage()->getDynamicVariable(
                        'wcf.clipboard.item.de.xxschrandxx.assets.asset.delete.confirmMessage',
                        [
                            'count' => $item->getCount(),
                        ]
                    )
                );
                break;
        }

        return $item;
    }

    /**
     * Returns the ids of the assets that can be trashed.
     * @return  int[]
     */
    public function validateTrash()
    {
        $objectIDs = [];

        /** @var \assets\data\asset\Asset $asset */
        foreach ($this->objects as $asset) {
            if (!$asset->isTrashed() && $asset->canTrash()) {
                $objectIDs[] = $asset->getObjectID();
            }
        }

        return $objectIDs;
    }

    /**
     * Returns the ids of the assets that can be restored.
     * @return  int[]
     */
    public function validateRestore()
    {
        return $this->validateDelete();
    }

    /**
     * Returns the ids of the assets that can be deleted.
     * @return  int[]
     */
    public function validateDelete()
    {
        $objectIDs = [];

        /** @var \assets\data\asset\Asset $asset */
        foreach ($this->objects as $asset) {
            if ($asset->isTrashed() && $asset->canDelete()) {
                $objectIDs[] = $asset->getObjectID();
            }
        }

        return $objectIDs;
    }
}
