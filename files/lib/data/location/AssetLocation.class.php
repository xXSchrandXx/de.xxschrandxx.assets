<?php

namespace assets\data\location;

use InvalidArgumentException;
use wcf\data\category\AbstractDecoratedCategory;
use wcf\system\category\CategoryPermissionHandler;
use wcf\system\WCF;

/**
 * @property    string  $address
 */
class AssetLocation extends AbstractDecoratedCategory
{
    protected $perms = [];

    private $validePerms = ['canViewLocation', 'canModifyLocation'];

    private $valideGroups = ['user', 'mod', 'admin'];

    public function canView($user = null)
    {
        return $this->getPermission('canViewLocation', 'user', $user);
    }

    public function canModify($user = null)
    {
        return $this->getPermission('canModifyLocation', 'mod', $user);
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
