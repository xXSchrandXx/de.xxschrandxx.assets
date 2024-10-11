<?php

namespace assets\acp\form;

use wcf\acp\form\AbstractCategoryEditForm;

class LocationEditForm extends AbstractCategoryEditForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.application.assets.location.list';

    /**
     * @inheritDoc
     */
    public $objectTypeName = 'de.xxschrandxx.assets.location';

    /**
     * @inheritDoc
     */
    public $pageTitle = 'wcf.acp.menu.link.application.assets.location.edit';
}
