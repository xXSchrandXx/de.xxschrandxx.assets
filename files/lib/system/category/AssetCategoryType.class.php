<?php

namespace assets\system\category;

use wcf\system\category\AbstractCategoryType;
use wcf\system\WCF;

class AssetCategoryType extends AbstractCategoryType
{
    /**
     * @inheritDoc
     */
    protected $hasDescription = false;

    /**
     * @inheritDoc
     */
    protected $forceDescription = false;

    /**
     * @inheritDoc
     */
    protected $langVarPrefix = 'assets.category';

    /**
     * @inheritDoc
     */
    protected $maximumNestingLevel = 9;

    /**
     * @inheritDoc
     */
    protected $objectTypes = [
        'com.woltlab.wcf.acl' => 'de.xxschrandxx.assets.category'
    ];

    /**
     * @inheritDoc
     */
    public function canAddCategory()
    {
        return $this->canEditCategory();
    }

    /**
     * @inheritDoc
     */
    public function canDeleteCategory()
    {
        return $this->canEditCategory();
    }

    /**
     * @inheritDoc
     */
    public function canEditCategory()
    {
        return WCF::getSession()->getPermission('admin.assets.canManageCategories');
    }
}
