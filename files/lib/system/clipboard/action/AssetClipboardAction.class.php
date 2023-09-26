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
    protected $actionClassActions = ['trash', 'restore', 'delete', 'audit', 'getLabel'];

    /**
     * @inheritDoc
     */
    protected $supportedActions = ['trash', 'restore', 'delete', 'audit', 'getLabel'];

    /**
     * @inheritDoc
     */
    protected $reloadPageOnSuccess = ['trash', 'restore', 'delete', 'audit'];

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
                            'count' => $item->getCount()
                        ]
                    )
                );
                $item->addInternalData(
                    'template',
                    WCF::getTPL()->fetch('__clipboardReasonField', 'assets')
                );
                break;
            case 'restore':
                $item->addInternalData(
                    'confirmMessage',
                    WCF::getLanguage()->getDynamicVariable(
                        'wcf.clipboard.item.de.xxschrandxx.assets.asset.restore.confirmMessage',
                        [
                            'count' => $item->getCount()
                        ]
                    )
                );
                $item->addInternalData(
                    'template',
                    WCF::getTPL()->fetch('__clipboardReasonField', 'assets')
                );
                break;
            case 'delete':
                $item->addInternalData(
                    'confirmMessage',
                    WCF::getLanguage()->getDynamicVariable(
                        'wcf.clipboard.item.de.xxschrandxx.assets.asset.delete.confirmMessage',
                        [
                            'count' => $item->getCount()
                        ]
                    )
                );
                break;
            case 'audit':
                $item->addInternalData(
                    'confirmMessage',
                    WCF::getLanguage()->getDynamicVariable(
                        'wcf.clipboard.item.de.xxschrandxx.assets.asset.audit.confirmMessage',
                        [
                            'count' => $item->getCount()
                        ]
                    )
                );
                $item->addInternalData(
                    'template',
                    WCF::getTPL()->fetch('__clipboardCommentField', 'assets')
                );
                break;
            case 'getLabel':
                $item->addInternalData(
                    'confirmMessage',
                    WCF::getLanguage()->getDynamicVariable(
                        'wcf.clipboard.item.de.xxschrandxx.assets.asset.getLabel.confirmMessage',
                        [
                            'count' => $item->getCount()
                        ]
                    )
                );
                $item->addInternalData(
                    'template',
                    WCF::getTPL()->fetch('__clipboardSkipFieldsField', 'assets')
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
        $objectIDs = [];

        /** @var \assets\data\asset\Asset $asset */
        foreach ($this->objects as $asset) {
            if ($asset->isTrashed() && $asset->canAudit()) {
                $objectIDs[] = $asset->getObjectID();
            }
        }

        return $objectIDs;
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

    /**
     * Returns the ids of the assets that can be audit.
     * @return  int[]
     */
    public function validateAudit()
    {
        $objectIDs = [];

        /** @var \assets\data\asset\Asset $asset */
        foreach ($this->objects as $asset) {
            if ($asset->canAudit()) {
                $objectIDs[] = $asset->getObjectID();
            }
        }

        return $objectIDs;
    }

    /**
     * Returns the ids of the assets that can be audit.
     * @return  int[]
     */
    public function validateGetLabel()
    {
        $objectIDs = [];

        /** @var \assets\data\asset\Asset $asset */
        foreach ($this->objects as $asset) {
            if ($asset->canView()) {
                $objectIDs[] = $asset->getObjectID();
            }
        }

        return $objectIDs;
    }
}
