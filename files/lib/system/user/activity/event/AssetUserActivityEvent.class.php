<?php

namespace assets\system\user\activity\event;

use assets\data\asset\Asset;
use wcf\system\SingletonFactory;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\WCF;

class AssetUserActivityEvent extends SingletonFactory implements IUserActivityEvent
{
    /**
     * @inheritDoc
     */
    public function prepare(array $events)
    {
        foreach ($events as $event) {
            $asset = new Asset($event->objectID);
            if ($asset === null) {
                $event->setIsOrphaned();
                continue;
            }
            if (!$asset->canView()) {
                continue;
            }
            $event->setIsAccessible();

            $text = WCF::getLanguage()->getDynamicVariable(
                'assets.asset.reventActivity.'.$event->action,
                ['asset' => $asset]
            );
            $event->setTitle($text);
            $event->setDescription($asset->getSimplifiedFormattedMessage());
        }
    }
}
