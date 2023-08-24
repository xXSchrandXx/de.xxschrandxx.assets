<?php

namespace assets\system\stat;

use wcf\system\stat\AbstractCommentStatDailyHandler;

class AssetCommentStatDailyHandler extends AbstractCommentStatDailyHandler
{
    /**
     * @inheritDoc
     */
    protected $objectType = 'de.xxschrandxx.assets.asset.comment';
}
