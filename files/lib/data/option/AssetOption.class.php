<?php

namespace assets\data\option;

use wcf\data\custom\option\CustomOption;

class AssetOption extends CustomOption
{
    /**
     * @inheritDoc
     */
    public static $databaseTableName = 'option';

    /**
     * @inheritDoc
     */
    public static function getDatabaseTableAlias()
    {
        return 'asset_option';
    }
}
