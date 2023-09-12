<?php

namespace assets\data\asset;

use assets\system\log\modification\AssetModificationLogHandler;
use assets\util\AssetUtil;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use DateTimeImmutable;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\attachment\AttachmentHandler;
use wcf\system\comment\CommentHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
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

        // count attachments
        if (isset($this->parameters['description_attachmentHandler']) && $this->parameters['description_attachmentHandler'] !== null) {
            $data['attachments'] = \count($this->parameters['description_attachmentHandler']);
        }

        /** @var \assets\data\asset\Asset */
        $asset = parent::create();

        // update attachments
        if (isset($this->parameters['description_attachmentHandler']) && $this->parameters['description_attachmentHandler'] !== null) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->parameters['description_attachmentHandler']->updateObjectID($asset->getObjectID());
        }

        // save embedded objects
        if (!empty($this->parameters['description_htmlInputProcessor'])) { 
            /** @noinspection PhpUndefinedMethodInspection */
            $this->parameters['description_htmlInputProcessor']->setObjectID($asset->getObjectID());
            if (MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['description_htmlInputProcessor'])) {
                (new AssetEditor($asset))->update(['hasEmbeddedObjects' => 1]);
            }
        }

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
        // count attachments
        if (isset($this->parameters['description_attachmentHandler']) && $this->parameters['description_attachmentHandler'] !== null) {
            $this->parameters['data']['attachments'] = \count($this->parameters['description_attachmentHandler']);
        }

        if (array_key_exists('description_htmlInputProcessor', $this->parameters)) {
            /** @var \wcf\system\html\input\HtmlInputProcessor */
            $htmlInputProcessor = $this->parameters['description_htmlInputProcessor'];
            $this->parameters['data']['description'] = $htmlInputProcessor->getHtml();
        }

        $logHandler = AssetModificationLogHandler::getInstance();
        $reason = '';
        if (!empty($this->parameters) && array_key_exists('data', $this->parameters) && array_key_exists('reason', $this->parameters['data'])) {
            $reason = $this->parameters['data']['reason'];
            unset($this->parameters['data']['reason']);
            foreach ($this->getObjects() as $object) {
                $logHandler->edit($object->getDecoratedObject(), $reason);
            }
        }

        parent::update();

        foreach ($this->getObjects() as $asset) {
            // update search index
            SearchIndexManager::getInstance()->set(
                'de.xxschrandxx.assets.asset',
                $asset->getObjectID(),
                $this->parameters['data']['description'] ?? $asset->getDescription(),
                $this->parameters['data']['title'] ?? $asset->getTitle(),
                $asset->getTime(),
                0,
                ''
           );

           // save embedded objects
            if (!empty($this->parameters['description_htmlInputProcessor'])) {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->parameters['description_htmlInputProcessor']->setObjectID($asset->getObjectID());
                if ($asset->hasEmbeddedObjects != MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['description_htmlInputProcessor'])) {
                    $asset->update([
                        'hasEmbeddedObjects' => $asset->hasEmbeddedObjects ? 0 : 1,
                    ]);
                }
            }
        }
    }

    /**
     * Validates permissions and parameters
     */
    public function validateAudit()
    {
        $this->readString('comment', true, 'data');

        // read objects
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        foreach ($this->getObjects() as $asset) {
            if (!$asset->getDecoratedObject()->canAudit()) {
                throw new PermissionDeniedException();
            }
        }
    }

    /**
     * Update assets audit status
     */
    public function audit()
    {
        foreach ($this->getObjects() as $asset) {
            if ($asset->isTrashed()) {
                continue;
            }
            $nextAuditDateTime = $asset->getDecoratedObject()->calculateNextAuditDateTime();

            $asset->update([
                'lastAudit' => (new DateTimeImmutable())->format(AssetUtil::LAST_AUDIT_FORMAT),
                'nextAudit' => $nextAuditDateTime->format(AssetUtil::NEXT_AUDIT_FORMAT)
            ]);

            $comment = '';
            if (!empty($this->parameters) && array_key_exists('data', $this->parameters) && array_key_exists('comment', $this->parameters['data'])) {
                $comment = $this->parameters['data']['comment'];
                unset($this->parameters['data']['comment']);
            }
            AssetModificationLogHandler::getInstance()->audit($asset->getDecoratedObject(), $comment);
        }
    }

    /**
     * Validates permissions and parameters
     */
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

    /**
     * Trashes asset
     */
    public function trash()
    {
        foreach ($this->getObjects() as $asset) {
            if ($asset->isTrashed()) {
                continue;
            }

            $asset->update([
                'isTrashed' => 1
            ]);

            $reason = '';
            if (!empty($this->parameters) && array_key_exists('data', $this->parameters) && array_key_exists('reason', $this->parameters['data'])) {
                $reason = $this->parameters['data']['reason'];
                unset($this->parameters['data']['reason']);
            }
            AssetModificationLogHandler::getInstance()->trash($asset->getDecoratedObject(), $reason);
        }
    }

    /**
     * Validates permissions and parameters
     */
    public function validateRestore()
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
            if (!$asset->getDecoratedObject()->canRestore()) {
                throw new PermissionDeniedException();
            }
        }
    }

    /**
     * Restores asset
     */
    public function restore()
    {
        foreach ($this->getObjects() as $asset) {
            if (!$asset->isTrashed()) {
                continue;
            }

            $asset->update([
                'isTrashed' => 0,
            ]);

            $reason = '';
            if (!empty($this->parameters) && array_key_exists('data', $this->parameters) && array_key_exists('reason', $this->parameters['data'])) {
                $reason = $this->parameters['data']['reason'];
                unset($this->parameters['data']['reason']);
            }
            AssetModificationLogHandler::getInstance()->restore($asset->getDecoratedObject(), $reason);
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

        // delete comments
        CommentHandler::getInstance()->deleteObjects('de.xxschrandxx.assets.asset.comment', $objectIDs);

        // update search index
        SearchIndexManager::getInstance()->delete('de.xxschrandxx.assets.asset', $objectIDs);

        // delete embedded objects
        MessageEmbeddedObjectManager::getInstance()->removeObjects('de.xxschrandxx.assets.asset', $objectIDs);

        // delete attachments
        AttachmentHandler::removeAttachments('de.xxschrandxx.assets.asset.attachment', $objectIDs);
    }
}
