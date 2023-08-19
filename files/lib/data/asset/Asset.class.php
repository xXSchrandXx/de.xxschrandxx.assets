<?php

namespace assets\data\asset;

use assets\data\category\AssetCategory;
use assets\data\location\AssetLocation;
use assets\page\AssetPage;
use assets\system\attachment\AssetAttachmentObjectType;
use assets\system\comment\manager\AssetCommentManager;
use DateTimeImmutable;
use InvalidArgumentException;
use wcf\data\attachment\AttachmentList;
use wcf\data\attachment\GroupedAttachmentList;
use wcf\data\comment\StructuredCommentList;
use wcf\data\DatabaseObject;
use wcf\data\IAccessibleObject;
use wcf\data\ICategorizedObject;
use wcf\data\ITitledLinkObject;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\UserProfile;
use wcf\system\attachment\AttachmentHandler;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

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
 * @property-read    int         $lastCommentTime
 * @property-read    int         $lastTimeModified
 * @property-read    int         $time
 */
class Asset extends DatabaseObject implements ITitledLinkObject, IAccessibleObject, ICategorizedObject
{
    protected ?AssetLocation $location;

    protected ?AssetCategory $category;

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
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getControllerLink(AssetPage::class, ['id' => $this->getObjectID()]);
    }

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
     * @return ?int
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
        if ($this->isBorrowed()) {
            throw new InvalidArgumentException('Asset is borrowed.');
        }

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
     * Returns parsed description
     * @return string
     */
    public function getDescription()
    {
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
     * Returns lastCommentTime timestamp
     * @return int
     */
    public function getLastCommentTime(): int
    {
        return $this->lastTimeModified;
    }


    /**
     * Returns last comment date
     * @return DateTimeImmutable
     */
    public function getLastCommentTimeDate(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->getLastCommentTime());
    }

    /**
     * Returns createdTimestamp
     * @return int
     */
    public function getCreatedTimestamp(): int
    {
        return $this->time;
    }

    /**
     * Returns creation date
     * @return DateTimeImmutable
     */
    public function getCreatedDate(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->getCreatedTimestamp());
    }

    /**
     * Returns lastTimeModified timestamp
     * @return int
     */
    public function getLastTimeModifiedTimestamp(): int
    {
        return $this->lastTimeModified;
    }


    /**
     * Returns last modification date
     * @return DateTimeImmutable
     */
    public function getLastTimeModifiedDate(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->getLastTimeModifiedTimestamp());
    }

    /**
     * @inheritDoc
     */
    public function isAccessible($user = null)
    {
        return $this->canView($user);
    }

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
}
