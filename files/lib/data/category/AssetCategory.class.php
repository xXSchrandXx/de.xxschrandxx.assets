<?php

namespace assets\data\category;

use InvalidArgumentException;
use wcf\data\category\AbstractDecoratedCategory;
use wcf\system\category\CategoryPermissionHandler;
use wcf\system\WCF;

class AssetCategory extends AbstractDecoratedCategory
{
    protected $perms = [];

    protected $validePerms = ['canViewCategory', 'canAddCategory', 'canModifyCategory', 'canTrashCategory', 'canDeleteCategory'];

    protected $valideGroups = ['user', 'mod', 'admin'];

    public function canView($user = null)
    {
        return $this->getPermission('canViewCategory', 'user', $user);
    }

    public function canAdd($user = null)
    {
        return $this->getPermission('canAddCategory', 'user', $user);
    }

    public function canModify($user = null)
    {
        return $this->getPermission('canModifyCategory', 'mod', $user);
    }

    public function canTrash($user = null)
    {
        return $this->getPermission('canTrashCategory', 'mod', $user);
    }

    public function canDelete($user = null)
    {
        return $this->getPermission('canDeleteCategory', 'admin', $user);
    }

    /**
     * @param string $perm
     * @param string $group
     * @param \wcf\data\user\User $user
     */
    public function getPermission($perm, $group = 'user', $user = null)
    {
        if (!in_array($perm, $this->validePerms)) {
            throw new InvalidArgumentException('Unknown Permission');
        }
        if (!in_array($group, $this->valideGroups)) {
            throw new InvalidArgumentException('Unknown Group');
        }

        if ($user === null) {
            $user = WCF::getUser();
        }

        if (!isset($this->perms[$user->userID])) {
            $this->perms[$user->userID] = CategoryPermissionHandler::getInstance()->getPermissions($this->getDecoratedObject(), $user);
        }

        if (isset($this->perms[$user->userID][$perm])) {
            return $this->perms[$user->userID][$perm];
        }

        if ($this->getParentCategory()) {
            return $this->getParentCategory()->getPermission($perm, $group, $user);
        }

        return true;
    }
}
