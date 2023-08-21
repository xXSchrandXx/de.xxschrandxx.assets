<?php

namespace assets\data\asset;

use assets\system\log\modification\AssetModificationLogHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\search\SearchIndexManager;

/**
 * @property    AssetEditor[]   $objects
 * @method      AssetEditor[]   getObjects()
 * @method      AssetEditor     getSingleObject()
 */
class AssetAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['mod.assets.canAdd'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.assets.canDelete'];

    /**
     * @inheritDoc
     */
    protected $className = AssetEditor::class;

    /**
     * @inheritDoc
     */
    protected $resetCache = ['create', 'delete', 'trash', 'restore', 'update'];

    /**
     * @inheritDoc
     */
    public function create()
    {
        if (array_key_exists('description_htmlInputProcessor', $this->parameters)) {
            /** @var \wcf\system\html\input\HtmlInputProcessor */
            $htmlInputProcessor = $this->parameters['description_htmlInputProcessor'];
            $this->parameters['data']['description'] = $htmlInputProcessor->getHtml();
        }

        /** @var \assets\data\asset\Asset */
        $asset = parent::create();
        // update search index
        SearchIndexManager::getInstance()->set(
            'de.xxschrandxx.assets.asset',
            $asset->getObjectID(),
            $asset->getDescription(),
            $asset->getTitle(),
            $asset->getTime(),
            0,
            ''
        );

        return $asset;
    }

    /**
     * @inheritDoc
     */
    public function validateUpdate()
    {
        $this->readString('reason', true, 'data');

        parent::validateUpdate();

        foreach ($this->getObjects() as $asset) {
            if (!$asset->getDecoratedObject()->canModify()) {
                throw new PermissionDeniedException();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function update()
    {
        $logHandler = AssetModificationLogHandler::getInstance();
        foreach ($this->getObjects() as $object) {
            $logHandler->edit($object->getDecoratedObject(), $this->parameters['data']['reason']);
        }
        unset($this->parameters['data']['reason']);

        if (array_key_exists('description_htmlInputProcessor', $this->parameters)) {
            /** @var \wcf\system\html\input\HtmlInputProcessor */
            $htmlInputProcessor = $this->parameters['description_htmlInputProcessor'];
            $this->parameters['data']['description'] = $htmlInputProcessor->getHtml();
        }

        parent::update();

        /** @var \assets\data\asset\Asset $asset */
        foreach ($this->getObjects() as $asset) {
            SearchIndexManager::getInstance()->set(
                'de.xxschrandxx.assets.asset',
                $asset->getObjectID(),
                $this->parameters['data']['description'] ?? $asset->getDescription(),
                $this->parameters['data']['title'] ?? $asset->getTitle(),
                $this->parameters['data']['time'] ?? $asset->getTime(),
                0,
                ''
           );
        }
    }

    public function validateTrash()
    {
        $this->readString('reason', true, 'data');

        // read objects
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        foreach ($this->getObjects() as $asset) {
            if (!$asset->getDecoratedObject()->canTrash()) {
                throw new PermissionDeniedException();
            }
        }
    }

    public function trash()
    {
        foreach ($this->getObjects() as $asset) {
            if ($asset->isTrashed()) {
                continue;
            }

            $asset->update([
                'isTrashed' => 1
            ]);

            AssetModificationLogHandler::getInstance()->trash($asset->getDecoratedObject(), $this->parameters['data']['reason']);
        }
    }

    public function validateRestore()
    {
        // read objects
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        foreach ($this->getObjects() as $asset) {
            if (!$asset->getDecoratedObject()->canDelete()) {
                throw new PermissionDeniedException();
            }
        }
    }

    public function restore()
    {
        foreach ($this->getObjects() as $asset) {
            if (!$asset->isTrashed()) {
                continue;
            }

            $asset->update([
                'isTrashed' => 0,
            ]);

            AssetModificationLogHandler::getInstance()->restore($asset->getDecoratedObject());
        }
    }

    /**
     * @inheritDoc
     */
    public function delete()
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        $objectIDs = $this->getObjectIDs();

        parent::delete();

        if (empty($objectIDs)) {
            return;
        }

        SearchIndexManager::getInstance()->delete('de.xxschrandxx.assets.asset', $objectIDs);
    }
}
