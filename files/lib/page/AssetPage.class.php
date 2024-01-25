<?php

namespace assets\page;

use assets\data\asset\Asset;
use assets\data\asset\modification\AssetModificationLogList;
use assets\data\asset\modification\ViewableAssetModificationLog;
use assets\system\comment\manager\AssetCommentManager;
use assets\system\option\AssetOptionHandler;
use wcf\data\attachment\AttachmentList;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\MetaTagHandler;
use wcf\system\style\StyleHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

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
     * @var ViewableAssetModificationLog[]
     */
    public $auditLogs;

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
        $assetModificationLogList->getConditionBuilder()->add("action != 'audit'");
        $assetModificationLogList->readObjects();
        $this->modificationLogs = $assetModificationLogList->getObjects();

        $assetAuditLogList = new AssetModificationLogList([$this->object->getObjectID()]);
        $assetAuditLogList->getConditionBuilder()->add("action = 'audit'");
        $assetAuditLogList->readObjects();
        $this->auditLogs = $assetAuditLogList->getObjects();

        // add meta tags
        MetaTagHandler::getInstance()->addTag(
            'og:title',
            'og:title',
            $this->object->getSubject() . ' - ' . WCF::getLanguage()->get(PAGE_TITLE),
            true
        );
        MetaTagHandler::getInstance()->addTag(
            'og:url',
            'og:url',
            $this->object->getLink(),
            true
        );
        MetaTagHandler::getInstance()->addTag(
            'og:type',
            'og:type',
            'thing',
            true
        );
        MetaTagHandler::getInstance()->addTag(
            'og:identifier',
            'og:identifier',
            StringUtil::decodeHTML(StringUtil::stripHTML(ASSETS_LEGACYID_ENABLED ? $this->object->getLegacyID() : $this->object->getObjectID()))
        );
        MetaTagHandler::getInstance()->addTag(
            'og:description',
            'og:description',
            StringUtil::decodeHTML(StringUtil::stripHTML($this->object->getExcerpt())),
            true
        );

        $attachmentList = new AttachmentList('de.xxschrandxx.assets.asset.attachment');
        $attachmentList->getConditionBuilder()->add('objectID = ?', [$this->object->getObjectID()]);
        if ($attachmentList->countObjects() !== 0) {
            $attachmentList->readObjects();
            /** @var \wcf\data\attachment\Attachment */
            $firstImageAttachment = null;
            foreach ($attachmentList->getObjects() as $attachment) {
                if ($attachment->isImage) {
                    $firstImageAttachment = $attachment;
                    break;
                }
            }
            if ($firstImageAttachment !== null) {
                $link = $firstImageAttachment->getLink();
                $height = $firstImageAttachment->height;
                $width = $firstImageAttachment->width;
            }
        } else {
            $style = StyleHandler::getInstance()->getStyle();
            if ($style === null) {
                // No image to link
                return;
            }
            $link = $style->getCoverPhotoUrl();
            $height = $style->getCoverPhotoHeight();
            $width = $style->getCoverPhotoWidth();
        }
        MetaTagHandler::getInstance()->addTag(
            'og:image',
            'og:image',
            StringUtil::decodeHTML(StringUtil::stripHTML($link)),
            true
        );
        MetaTagHandler::getInstance()->addTag(
            'og:image:height',
            'og:image:height',
            StringUtil::decodeHTML(StringUtil::stripHTML($height)),
            true
        );        MetaTagHandler::getInstance()->addTag(
            'og:image:width',
            'og:image:width',
            StringUtil::decodeHTML(StringUtil::stripHTML($width)),
            true
        );
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        $assetCommentManager = AssetCommentManager::getInstance();
        $commentCanAdd = $assetCommentManager->canAdd($this->object->getObjectID());
        $optionHandler = new AssetOptionHandler(false);
        $optionHandler->setAsset($this->object);
        WCF::getTPL()->assign([
            'object' => $this->object,
            'options' => $optionHandler->options,
            'commentCanAdd' => $commentCanAdd,
            'commentManager' => $assetCommentManager,
            'commentObjectTypeID' => $this->object->getCommentObjectTypeID(),
            'commentList' => $this->object->getReadCommentList(),
            'lastCommentTime' => $this->object->getLastCommentDateTime() !== null ? $this->object->getLastCommentDateTime() : 0,
            'commentObjectID' => $this->object->getObjectID(),
            'commentContainerID' => 'assetComments',
            'modificationLogs' => $this->modificationLogs,
            'auditLogs' => $this->auditLogs,
            'highlightTitle' => $this->highlightTitle
        ]);
    }
}
