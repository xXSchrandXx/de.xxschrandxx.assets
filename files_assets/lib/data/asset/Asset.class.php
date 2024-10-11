<?php

namespace assets\data\asset;

use assets\data\category\AssetCategory;
use assets\data\location\AssetLocation;
use assets\data\option\AssetOption;
use assets\page\AssetPage;
use assets\system\comment\manager\AssetCommentManager;
use assets\util\AssetUtil;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use InvalidArgumentException;
use wcf\data\attachment\GroupedAttachmentList;
use wcf\data\comment\StructuredCommentList;
use wcf\data\DatabaseObject;
use wcf\data\IAccessibleObject;
use wcf\data\ICategorizedObject;
use wcf\data\ITitledLinkObject;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\search\ISearchResultObject;
use wcf\data\user\UserProfile;
use wcf\system\benchmark\Benchmark;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * @property-read    int         $assetID
 * @property-read    string|null $legacyID
 * @property-read    int         $categoryID
 * @property-read    string      $title
 * @property-read    int         $amount
 * @property-read    int|null    $locationID
 * @property-read    string|null $description
 * @property-read    int         $isTrashed
 * @property-read    int         $comments
 * @property-read    string|null $lastComment
 * @property         string|null $nextAudit
 * @property-read    string      $lastAudit
 * @property-read    string      $lastModification
 * @property-read    string      $time
 * @property-read    int         $attachments
 * @property-read    int         $hasEmbeddedObjects
 */
class Asset extends DatabaseObject implements ITitledLinkObject, IAccessibleObject, ICategorizedObject, ISearchResultObject
{
    protected ?AssetLocation $location;

    protected ?AssetCategory $category;

    protected ?array $optionValues = null;

    /* ITitledLinkObject */
    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function getLink($query = ''): string
    {
        $parameters = [
            'id' => $this->getObjectID(),
            'object' => $this,
            'forceFrontend' => true
        ];

        if ($query) {
            $parameters['highlight'] = \urlencode($query);
        }
        return LinkHandler::getInstance()->getControllerLink(AssetPage::class, $parameters);
    }
    /* /ITitledLinkObject */

    /* IAccessibleObject */
    /**
     * @inheritDoc
     */
    public function isAccessible($user = null)
    {
        return $this->canView($user);
    }
    /* /IAccessibleObject */

    /* ICategorizedObject */
    /**
     * Returns categoryID
     * @return int
     */
    public function getCategoryID(): int
    {
        return $this->categoryID;
    }

    /**
     * Returns AssetCategory
     * @return ?AssetCategory
     */
    public function getCategory(): AssetCategory
    {
        if (!isset($this->category)) {
            if ($this->categoryID) {
                $this->category = AssetCategory::getCategory($this->categoryID);
            } else {
                $this->category = null;
            }
        }

        return $this->category;
    }
    /* /ICategorizedObject */

    /* ISearchResultObject getLink() -> ITitledLinkObject */
    /**
     * @inheritDoc
     */
    public function getUserProfile()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSubject()
    {
        return $this->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getTime()
    {
        return $this->getCreatedDateTime()->getTimestamp();
    }

    /**
     * @inheritDoc
     */
    public function getObjectTypeName()
    {
        return 'de.xxschrandxx.assets.asset';
    }

    /**
     * @inheritDoc
     */
    public function getFormattedMessage()
    {
        return $this->getDescription();
    }

    /**
     * Returns a simplified version of the formatted message.
     *
     * @return  string
     */
    public function getSimplifiedFormattedMessage()
    {
        $processor = new HtmlOutputProcessor();
        $processor->setOutputType('text/simplified-html');
        $processor->process(
            $this->getRawDescription(),
            'de.xxschrandxx.assets.asset',
            $this->getObjectID()
        );

        return $processor->getHtml();
    }

    /**
     * @inheritDoc
     */
    public function getContainerTitle()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getContainerLink()
    {
        return '';
    }
    /* /ISearchResultObject */

    /**
     * Returns legacyID
     * @return ?string
     */
    public function getLegacyID(): ?string
    {
        return $this->legacyID;
    }

    /**
     * Returns amount
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * Returns locationID
     * @return ?int
     * @throws InvalidArgumentException If the Asset is borrowed
     */
    public function getLocationID(): ?int
    {
        return $this->locationID;
    }

    /**
     * Returns AssetLocation
     * @return ?AssetLocation
     */
    public function getLocation(): ?AssetLocation
    {
        if (!isset($this->location)) {
            if ($this->locationID) {
                $this->location = AssetLocation::getCategory($this->locationID);
            } else {
                $this->location = null;
            }
        }

        return $this->location;
    }

    /**
     * Weather this asset is thrashed
     * @return bool
     */
    public function isTrashed(): bool
    {
        return $this->isTrashed;
    }

    /**
     * Returns the message object type
     * @return \wcf\data\object\type\ObjectType
     */
    public function getDescriptionObjectType()
    {
        return ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.message', 'de.xxschrandxx.assets.asset');
    }

    /**
     * Returns raw description
     * @return string
     */
    public function getRawDescription()
    {
        return $this->description;
    }

    /**
     * true if embedded objects have already been loaded
     * @var bool
     */
    protected $embeddedObjectsLoaded = false;

    /**
     * Loads the embedded objects.
     */
    public function loadEmbeddedObjects()
    {
        if ($this->hasEmbeddedObjects && !$this->embeddedObjectsLoaded) {
            MessageEmbeddedObjectManager::getInstance()->loadObjects('de.xxschrandxx.assets.asset', [$this->getObjectID()]);
            $this->embeddedObjectsLoaded = true;
        }
    }

    /**
     * Returns parsed description
     * @return string
     */
    public function getDescription()
    {
        $this->loadEmbeddedObjects();

        $htmlProcessor = new HtmlOutputProcessor();
        $htmlProcessor->process(
            $this->getRawDescription(),
            'de.xxschrandxx.assets.asset',
            $this->getObjectID()
        );
        return $htmlProcessor->getHtml();
    }

    /**
     * Returns the attachment object type
     * @return \wcf\data\object\type\ObjectType
     */
    public function getAttachmentObjectType()
    {
        return ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.attachment.objectType', 'de.xxschrandxx.assets.asset.attachment');
    }

    /**
     * Returns a unread attachment list
     * @return GroupedAttachmentList
     */
    public function getAttachmentList()
    {
        $attachmentList = new GroupedAttachmentList('de.xxschrandxx.assets.asset.attachment');
        $attachmentList->getConditionBuilder()->add('objectID = ?', [$this->getObjectID()]);
        return $attachmentList;
    }

    /**
     * Returns a read attachment list
     * @return GroupedAttachmentList
     */
    public function getReadAttachmentList()
    {
        $attachmentList = $this->getAttachmentList();
        $attachmentList->readObjects();
        return $attachmentList;
    }

    /**
     * Returns comment count
     * @return int
     */
    public function getCommentCount(): int
    {
        return $this->comments;
    }

    /**
     * Returns the comment object type
     * @return \wcf\data\object\type\ObjectType
     */
    public function getCommentObjectType()
    {
        return ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.comment.commentableContent', 'de.xxschrandxx.assets.asset.comment');
    }

    /**
     * Returns the comment object type id
     * @return int
     */
    public function getCommentObjectTypeID(): int
    {
        return $this->getCommentObjectType()->getObjectID();
    }

    /**
     * Returns a unread comment list
     * @return StructuredCommentList
     */
    public function getCommentList()
    {
        return new StructuredCommentList(
            AssetCommentManager::getInstance(),
            $this->getCommentObjectTypeID(),
            $this->getObjectID()
        );
    }

    /**
     * Returns a read comment list
     * @return StructuredCommentList
     */
    public function getReadCommentList()
    {
        $commentList = $this->getCommentList();
        $commentList->readObjects();
        return $commentList;
    }

    /**
     * @inheritDoc
     */
    public function getExcerpt($maxLength = 255)
    {
        return StringUtil::truncateHTML($this->getSimplifiedFormattedMessage(), $maxLength);
    }

    protected ?DateTimeImmutable $lastCommentDateTime = null;

    /**
     * Returns last comment date
     * @return ?DateTimeImmutable
     */
    public function getLastCommentDateTime(): ?DateTimeImmutable
    {
        if (!isset($this->lastCommentDateTime) && isset($this->lastComment)) {
            $this->lastCommentDateTime = new DateTimeImmutable($this->lastComment, $this->getDateTimeZone());
        }
        return $this->lastCommentDateTime;
    }

    protected DateTimeImmutable $createdDateTime;

    /**
     * Returns creation date
     * @return DateTimeImmutable
     */
    public function getCreatedDateTime(): DateTimeImmutable
    {
        if (!isset($this->createdDateTime)) {
            $this->createdDateTime = new DateTimeImmutable($this->time, $this->getDateTimeZone());
        }
        return $this->createdDateTime;
    }

    protected DateTimeImmutable $lastModificationDateTime;

    /**
     * Returns last modification date
     * @return DateTimeImmutable
     */
    public function getLastModificationDateTime(): DateTimeImmutable
    {
        if (!isset($this->lastModificationDateTime)) {
            $this->lastModificationDateTime = new DateTimeImmutable($this->lastModification, $this->getDateTimeZone());
        }
        return $this->lastModificationDateTime;
    }

    protected DateTimeImmutable $lastAuditDateTime;

    /**
     * Returns last audit date
     * @return DateTimeImmutable
     */
    public function getLastAuditDateTime(): DateTimeImmutable
    {
        if (!isset($this->lastAuditDateTime) && isset($this->lastAudit)) {
            $this->lastAuditDateTime = new DateTimeImmutable($this->lastAudit, $this->getDateTimeZone());
        }
        return $this->lastAuditDateTime;
    }

    protected ?DateTimeImmutable $nextAuditDateTime = null;

    /**
     * Returns next audit date
     * @return ?DateTimeImmutable
     */
    public function getNextAuditDateTime(): ?DateTimeImmutable
    {
        if (!isset($this->nextAuditDateTime)) {
            $this->nextAuditDateTime = new DateTimeImmutable($this->nextAudit, $this->getDateTimeZone());
        }
        return $this->nextAuditDateTime;
    }

    /**
     * Calculates the current datetime for next audit.
     * This does not modify data in database
     * @return DateTimeImmutable
     * @throws Exception @link(DateTime::__construct()), @link(DateInterval::__construct())
     */
    public function calculateNextAuditDateTime(): DateTimeImmutable
    {
        return AssetUtil::calculateNextAuditDateTime($this->getLastAuditDateTime());
    }

    protected DateTimeZone $zone;

    public function getDateTimeZone(): DateTimeZone
    {
        if (!isset($this->zone)) {
            $this->zone = AssetUtil::getDateTimeZone();
        }
        return $this->zone;
    }

    /**
     * @return string
     */
    public function getQRCode()
    {
        $path = ASSETS_DIR . 'images/qr/' . $this->getObjectID() . '.svg';
        if (!file_exists($path)) {
            if (WCF::benchmarkIsEnabled()) {
                $benchmarkIndex = Benchmark::getInstance()->start('AssetQRCode', Benchmark::TYPE_OTHER);
            }

            // load php-qrcode library
            require_once(ASSETS_DIR . 'lib/system/api/autoload.php');

            $options = new QROptions();
            $options->outputType = QRCode::OUTPUT_MARKUP_SVG;
            $options->eccLevel = QRCode::ECC_L;

            $qrCode = new QRCode($options);
            $qrCode->render($this->getLink(), $path);
            if (isset($benchmarkIndex)) {
                Benchmark::getInstance()->stop($benchmarkIndex);
            }
        }

        return WCF::getPath('assets') . 'images/qr/' . $this->getObjectID() . '.svg';
    }

    /* Permissions */
    /**
     * checks weather user can view
     * @param User $user
     * @return bool
     */
    public function canView($user = null)
    {
        if (!$this->checkPermission('canView', 'user', $user)) {
            return false;
        }

        if ($this->isTrashed() && !$this->checkPermission('canDelete', 'admin', $user)) {
            return false;
        }

        if ($this->getCategory()->canView($user) && $this->getLocation()->canView($user)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * checks weather user can add
     * @param User $user
     * @return bool
     */
    public function canAdd($user = null)
    {
        if (!$this->checkPermission('canAdd', 'mod', $user)) {
            return false;
        }

        if ($this->getCategory()->canAdd($user)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * checks weather user can modify
     * @param User $user
     * @return bool
     */
    public function canModify($user = null)
    {
        if (!$this->checkPermission('canModify', 'mod', $user)) {
            return false;
        }

        if ($this->getCategory()->canModify($user) && $this->getLocation()->canModify($user)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * checks weather user can audit
     * @param User $user
     * @return bool
     */
    public function canAudit($user = null)
    {
        return $this->canModify($user);
    }

    /**
     * checks weather user can restore
     * @param User $user
     * @return bool
     */
    public function canRestore($user = null)
    {
        return $this->canDelete($user);
    }

    /**
     * checks weather user can trash
     * @param User $user
     * @return bool
     */
    public function canTrash($user = null)
    {
        if (!$this->checkPermission('canTrash', 'mod', $user)) {
            return false;
        }

        if ($this->getCategory()->canTrash($user)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * checks weather user can delete
     * @param User $user
     * @return bool
     */
    public function canDelete($user = null)
    {
        if (!$this->checkPermission('canDelete', 'admin', $user)) {
            return false;
        }

        if ($this->getCategory()->canDelete($user)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * helper method for permission check
     * @param $perm
     * @param $group ['user', 'mod', 'admin]
     * @param $user
     * @return bool
     * @throws InvalidArgumentException
     */
    protected function checkPermission($perm, $group = 'user', $user = null)
    {
        if (!in_array($group, ['user', 'mod', 'admin'])) {
            throw new InvalidArgumentException('Unknown Group');
        }

        if ($user === null) {
            $user = WCF::getUser();
        }

        if ($user->userID === WCF::getSession()->getUser()->userID) {
            return WCF::getSession()->getPermission($group . '.assets.' . $perm);
        } else {
            $userProfile = new UserProfile($user);

            return $userProfile->getPermission($group . 'assets.' . $perm);
        }
    }
    /* /Permissions */

    /* AssetOptions */
    /**
     * Sets the value of the option with the given id
     * @throws InvalidArgumentException
     */
    public function setOptionValue(int $optionID, $optionValue): void
    {
        if ($optionValue === null) {
            throw new InvalidArgumentException("optionValue cannot be null");
        }
        $sql = "INSERT INTO     assets" . WCF_N . "_option_value
                                (assetID, optionID, optionValue)
                VALUES          (?, ?, ?)";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        $statement->execute([
            $this->getObjectID(),
            $optionID,
            $optionValue,
        ]);
        WCF::getDB()->commitTransaction();
    }

    /**
     * Returns the value of the option with the given id or an empty string if no value has been set.
     */
    public function getOptionValue(int $optionID): string
    {
        if ($this->optionValues === null) {
            $this->optionValues = [];
            $sql = "SELECT  optionID, optionValue
                    FROM    assets" . WCF_N . "_option_value
                    WHERE   assetID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$this->assetID]);

            $this->optionValues = $statement->fetchMap('optionID', 'optionValue');
        }

        if (isset($this->optionValues[$optionID])) {
            return $this->optionValues[$optionID];
        }

        return '';
    }

    /**
     * Returns the formatted value of the option with the given id or an empty string if no value has been set.
     */
    public function getFormattedOptionValue(AssetOption $option, bool $forcePlaintext = false): string
    {
        $option->setOptionValue($this->getOptionValue($option->getObjectID()));
        return $option->getFormattedOptionValue($forcePlaintext);
    }
    /* /AssetOptions */
}
