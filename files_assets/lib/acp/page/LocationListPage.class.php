<?php

namespace assets\acp\page;

use wcf\acp\page\AbstractCategoryListPage;

class LocationListPage extends AbstractCategoryListPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.application.assets.location.list';

    /**
     * @inheritDoc
     */
    public $addController = 'LocationAdd';

    /**
     * @inheritDoc
     */
    public $editController = 'LocationEdit';

    /**
     * @inheritDoc
     */
    public $pageTitle = 'wcf.acp.menu.link.application.assets.location.list';

    /**
     * @inheritDoc
     */
    public $objectTypeName = 'de.xxschrandxx.assets.location';
}
