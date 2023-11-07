<?php

namespace assets\system\user\activity\event;

use assets\data\asset\Asset;
use wcf\system\cache\runtime\ViewableCommentRuntimeCache;
use wcf\system\SingletonFactory;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\WCF;

class AssetCommentUserActivityEvent extends SingletonFactory implements IUserActivityEvent
{
    /**
     * @inheritDoc
     */
    public function prepare(array $events)
    {
        $commentIDs = [];
        foreach ($events as $event) {
            $commentIDs[] = $event->objectID;
        }

        // fetch comments
        $comments = ViewableCommentRuntimeCache::getInstance()->getObjects($commentIDs);

        // set message
        foreach ($events as $event) {
            if (!isset($comments[$event->objectID])) {
                continue;
            }

            // short output
            $comment = $comments[$event->objectID];

            $asset = new Asset($comment->objectID);
            if ($asset === null) {
                $event->setIsOrphaned();
                continue;
            }

            if (!$asset->canView()) {
                continue;
            }
            $event->setIsAccessible();

            $text = WCF::getLanguage()->getDynamicVariable('assets.asset.reventActivity.comment', [
                'asset' => $asset,
                'commentID' => $comment->commentID,
            ]);
            $event->setTitle($text);
            $event->setDescription($comment->getExcerpt());
        }
    }
}
