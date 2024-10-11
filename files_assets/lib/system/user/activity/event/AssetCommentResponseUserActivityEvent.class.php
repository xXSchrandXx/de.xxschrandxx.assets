<?php

namespace assets\system\user\activity\event;

use assets\data\asset\Asset;
use wcf\system\SingletonFactory;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\user\activity\event\TCommentResponseUserActivityEvent;
use wcf\system\WCF;

class AssetCommentResponseUserActivityEvent extends SingletonFactory implements IUserActivityEvent
{
    use TCommentResponseUserActivityEvent;

    /**
     * @inheritDoc
     */
    public function prepare(array $events)
    {
        $this->readResponseData($events);

        foreach ($events as $event) {
            if (!isset($this->responses[$event->objectID])) {
                $event->setIsOrphaned();
                continue;
            }
            $response = $this->responses[$event->objectID];
            $comment = $this->comments[$response->commentID];
            if (!isset($this->commentAuthors[$comment->userID])) {
                continue;
            }
            $asset = new Asset($comment->objectID);

            if ($asset === null) {
                $event->setIsOrphaned();
                continue;
            }

            if (!$asset->canView()) {
                continue;
            }
            $event->setIsAccessible();

            $text = WCF::getLanguage()->getDynamicVariable(
                'assets.asset.reventActivity.commentResponse',
                [
                    'commentAuthor' => $this->commentAuthors[$comment->userID],
                    'commentID' => $comment->commentID,
                    'responseID' => $response->responseID,
                    'asset' => $asset,
                ]
            );
            $event->setTitle($text);
            $event->setDescription($response->getExcerpt());
        }
    }
}
