<?php

namespace assets\system;

use assets\page\AssetListPage;
use wcf\system\application\AbstractApplication;

final class ASSETSCore extends AbstractApplication
{
    /**
     * @inheritDoc
     */
    protected $primaryController = AssetListPage::class;
}
