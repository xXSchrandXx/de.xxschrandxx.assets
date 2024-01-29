<?php

namespace assets\data\asset;

use assets\system\log\modification\AssetModificationLogHandler;
use assets\util\AssetUtil;
use DateTimeImmutable;
use Dompdf\Adapter\CPDF;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\application\ApplicationHandler;
use wcf\system\attachment\AttachmentHandler;
use wcf\system\comment\CommentHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\search\SearchIndexManager;
use wcf\system\template\ACPTemplateEngine;
use wcf\system\template\TemplateEngine;
use wcf\system\user\activity\event\UserActivityEventHandler;
use wcf\system\WCF;

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

        $options = null;

        // update options
        if (!empty($this->parameters) && array_key_exists('data', $this->parameters) && array_key_exists('options', $this->parameters['data'])) {
            $options = $this->parameters['data']['options'];
            unset($this->parameters['data']['options']);
        }

        /** @var \assets\data\asset\Asset */
        $asset = parent::create();

        if ($options !== null) {
            $sql = "INSERT INTO     assets" . WCF_N . "_option_value
                                    (assetID, optionID, optionValue)
                    VALUES          (?, ?, ?)";
            $statement = WCF::getDB()->prepareStatement($sql);

            WCF::getDB()->beginTransaction();
            foreach ($options as $optionID => $optionValue) {
                if ($optionValue !== null) {
                    $statement->execute([
                        $asset->getObjectID(),
                        $optionID,
                        $optionValue,
                    ]);
                }
            }
            WCF::getDB()->commitTransaction();
        }

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

        // add user activity
        UserActivityEventHandler::getInstance()->fireEvent(
            'de.xxschrandxx.assets.asset.recentActivityEvent',
            $asset->getObjectID(),
            null,
            $this->parameters['data']['userID'] ?? WCF::getUser()->getUserID(),
            TIME_NOW,
            [
                'action' => 'create'
            ]
        );

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

        // update option values
        if (!empty($this->parameters) && array_key_exists('data', $this->parameters) && array_key_exists('options', $this->parameters['data'])) {
            WCF::getDB()->beginTransaction();

            $sql = "DELETE FROM     assets" . WCF_N . "_option_value
                    WHERE           assetID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            foreach ($this->getObjects() as $object) {
                $statement->execute([$object->getObjectID()]);
            }

            $sql = "INSERT INTO     assets" . WCF_N . "_option_value
                                    (assetID, optionID, optionValue)
                    VALUES          (?, ?, ?)";
            $statement = WCF::getDB()->prepareStatement($sql);
            foreach ($this->parameters['data']['options'] as $optionID => $optionValue) {
                foreach ($this->getObjects() as $object) {
                    $statement->execute([
                        $object->getObjectID(),
                        $optionID,
                        $optionValue,
                    ]);
                }
            }

            WCF::getDB()->commitTransaction();
            unset($this->parameters['data']['options']);
        }

        parent::update();

        foreach ($this->getObjects() as $asset) {
            // add user activity
            UserActivityEventHandler::getInstance()->fireEvent(
                'de.xxschrandxx.assets.asset.recentActivityEvent',
                $asset->getObjectID(),
                null,
                $this->parameters['data']['userID'] ?? WCF::getUser()->getUserID(),
                TIME_NOW,
                [
                    'action' => 'update'
                ]
            );

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
            // add log
            AssetModificationLogHandler::getInstance()->audit($asset->getDecoratedObject(), $comment);
            // add user activity
            UserActivityEventHandler::getInstance()->fireEvent(
                'de.xxschrandxx.assets.asset.recentActivityEvent',
                $asset->getObjectID(),
                null,
                $this->parameters['data']['userID'] ?? WCF::getUser()->getUserID(),
                TIME_NOW,
                [
                    'action' => 'audit'
                ]
            );
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
            // add log
            AssetModificationLogHandler::getInstance()->trash($asset->getDecoratedObject(), $reason);
            // add user activity
            UserActivityEventHandler::getInstance()->fireEvent(
                'de.xxschrandxx.assets.asset.recentActivityEvent',
                $asset->getObjectID(),
                null,
                $this->parameters['data']['userID'] ?? WCF::getUser()->getUserID(),
                TIME_NOW,
                [
                    'action' => 'trash'
                ]
            );
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
            // add log
            AssetModificationLogHandler::getInstance()->restore($asset->getDecoratedObject(), $reason);
            // add user activity
            UserActivityEventHandler::getInstance()->fireEvent(
                'de.xxschrandxx.assets.asset.recentActivityEvent',
                $asset->getObjectID(),
                null,
                $this->parameters['data']['userID'] ?? WCF::getUser()->getUserID(),
                TIME_NOW,
                [
                    'action' => 'restore'
                ]
            );
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

        // delete user activity
        UserActivityEventHandler::getInstance()->removeEvents(
            'de.xxschrandxx.assets.asset.recentActivityEvent',
            $objectIDs
        );
    }

    /**
     * Validates permissions and parameters
     */
    public function validateGetLabel()
    {
        $this->readInteger('skipFields', true, 'data');

        // read objects
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        foreach ($this->getObjects() as $asset) {
            if (!$asset->getDecoratedObject()->canView()) {
                throw new PermissionDeniedException();
            }
        }
    }

    /**
     * Returns pdf
     * @return string
     */
    public function getLabel()
    {
        // load dompdf library
        require_once(ASSETS_DIR . 'lib/system/api/autoload.php');

        // generate page size
        $size = CPDF::$PAPER_SIZES[ASSETS_LABEL_FORMAT];
        if (ASSETS_LABEL_ORIENTATION == 'landscape') {
            $pageHeight = $size[2] - $size[0];
            $pageWidth = $size[3] - $size[1];
        } else {
            $pageWidth = $size[2] - $size[0];
            $pageHeight = $size[3] - $size[1];
        }

        // calculate label spaces to skip
        $dummys = [];
        if (isset($this->parameters['data']['skipFields']) && is_numeric($this->parameters['data']['skipFields']) && $this->parameters['data']['skipFields'] > 0) {
            for ($i = 0; $i < $this->parameters['data']['skipFields']; $i++) {
                $dummys[] = 'dummy' . $i;
            }
        }

        // load template engine
        $tplEngine = TemplateEngine::getInstance();
        // support acp bulk processing
        if (\class_exists(\wcf\system\WCFACP::class, false) || !PACKAGE_ID) {
            $tplEngine = ACPTemplateEngine::getInstance();
            $tplEngine->addApplication('assets', ASSETS_DIR . 'templates/');
        }
        $chunks = array_chunk(array_merge($dummys, $this->getObjects()), ASSETS_LABEL_PER_PAGE);

        // add dummys
        foreach ($chunks as &$chunk) {
            for ($i = count($chunk); $i < ASSETS_LABEL_PER_PAGE; $i++) {
                array_push($chunk, 'dummy');
            }
        }

        return $tplEngine->fetch('__label', 'assets', [
            'logo' => ApplicationHandler::getInstance()->getApplication("assets")->getPageURL() . "/" . ASSETS_LABEL_LOGO,
            'chunks' => $chunks,
            'pageWidth' => $pageWidth,
            'pageHeight' => $pageHeight,
            'fontFamily' => ''
        ], true);
    }
}
