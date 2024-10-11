<?php

namespace assets\acp\form;

use wcf\acp\form\AbstractCategoryAddForm;

class LocationAddForm extends AbstractCategoryAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.application.assets.location.add';

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
    public $listController = 'LocationList';

    /**
     * @inheritDoc
     */
    public $objectTypeName = 'de.xxschrandxx.assets.location';

    /**
     * @inheritDoc
     */
    public $pageTitle = 'wcf.acp.menu.link.application.assets.location.add';
}
