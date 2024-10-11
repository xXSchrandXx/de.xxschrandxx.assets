<?php

namespace assets\acp\form;

use wcf\acp\form\AbstractBulkProcessingForm;

class BulkProcessForm extends AbstractBulkProcessingForm
{
    /**
     * @inheritDoc
     */
    public $templateNameApplication = 'wcf';

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.application.assets.bulkprocess';

    /**
     * @inheritDoc
     */
    public $objectTypeName = 'de.xxschrandxx.assets.asset';
}
