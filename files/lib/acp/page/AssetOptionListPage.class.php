<?php

namespace assets\acp\page;

use assets\data\option\AssetOptionList;
use wcf\page\SortablePage;

class AssetOptionListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.application.assets.option.list';

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'showOrder';

    /**
     * @inheritDoc
     */
    public $itemsPerPage = \PHP_INT_MAX;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.assets.canManageOption'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = AssetOptionList::class;

    /**
     * @inheritDoc
     */
    public $validSortFields = ['optionID', 'optionTitle', 'optionType', 'showOrder'];

}
