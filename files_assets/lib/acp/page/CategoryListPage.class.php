<?php

namespace assets\acp\page;

use wcf\acp\page\AbstractCategoryListPage;

class CategoryListPage extends AbstractCategoryListPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.application.assets.category.list';

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
    public $pageTitle = 'wcf.acp.menu.link.application.assets.category.list';

    /**
     * @inheritDoc
     */
    public $objectTypeName = 'de.xxschrandxx.assets.category';
}
