<?php

namespace assets\system\box;

use assets\system\cache\builder\StatsCacheBuilder;
use wcf\system\box\AbstractBoxController;
use wcf\system\WCF;

class AssetStatisticBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected static $supportedPositions = [
        'contentTop',
        'contentBottom'
    ];

    /**
     * @inheritDoc
     */
    protected function loadContent()
    {
        $this->content = WCF::getTPL()->fetch(
            'boxAssetStatistic',
            'assets',
            [
                'stats' => StatsCacheBuilder::getInstance()->getData()
            ],
            true
        );
    }
}