<?php

namespace assets\acp\form;

use wcf\acp\form\AbstractCategoryAddForm;

class CategoryAddForm extends AbstractCategoryAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.application.assets.category.add';

    /**
     * @inheritDoc
     */
    public $addController = 'CategoryAdd';

    /**
     * @inheritDoc
     */
    public $editController = 'CategoryEdit';

    /**
     * @inheritDoc
     */
    public $listController = 'CategoryList';

    /**
     * @inheritDoc
     */
    public $objectTypeName = 'de.xxschrandxx.assets.category';
}
