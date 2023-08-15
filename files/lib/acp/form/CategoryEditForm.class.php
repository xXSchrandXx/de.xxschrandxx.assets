<?php

namespace assets\acp\form;

use wcf\acp\form\AbstractCategoryEditForm;

class CategoryEditForm extends AbstractCategoryEditForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.application.assets.category.list';

    /**
     * @inheritDoc
     */
    public $objectTypeName = 'de.xxschrandxx.assets.category';
}
