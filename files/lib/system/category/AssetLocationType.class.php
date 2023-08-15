<?php

namespace assets\system\category;

use wcf\system\category\AbstractCategoryType;
use wcf\system\WCF;

class AssetLocationType extends AbstractCategoryType
{
    /**
     * @inheritDoc
     */
    protected $forceDescription = true;

    /**
     * @inheritDoc
     */
    protected $hasDescription = true;

    /**
     * @inheritDoc
     */
    protected $i18nLangVarCategory = 'assets.location';

    /**
     * @inheritDoc
     */
    protected $langVarPrefix = 'assets.location';

    /**
     * @inheritDoc
     */
    protected $maximumNestingLevel = 9;

    /**
     * @inheritDoc
     */
    protected $objectTypes = [
        'com.woltlab.wcf.acl' => 'de.xxschrandxx.assets.location'
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
        return WCF::getSession()->getPermission('admin.assets.canManageLocations');
    }
}
