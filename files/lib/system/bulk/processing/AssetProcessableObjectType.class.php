<?php

namespace assets\system\bulk\processing;

use wcf\system\bulk\processing\AbstractBulkProcessableObjectType;

class AssetProcessableObjectType extends AbstractBulkProcessableObjectType
{
    /**
     * @inheritDoc
     */
    protected $templateName = 'assetConditions';
}
