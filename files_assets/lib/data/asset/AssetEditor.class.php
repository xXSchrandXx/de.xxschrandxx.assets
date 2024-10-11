<?php

namespace assets\data\asset;

use assets\util\AssetUtil;
use DateTime;
use DateTimeImmutable;
use wcf\data\DatabaseObjectEditor;

/**
 * @property    Asset   $object
 * @method      Asset   getDecoratedObject()
 * @mixin       Asset
 */
class AssetEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Asset::class;

    /**
     * @inheritDoc
     */
    public static function create(array $parameters = [])
    {
        // Set default dates
        $now = new DateTimeImmutable('now', AssetUtil::getDateTimeZone());
        if (!array_key_exists('lastModification', $parameters)) {
            $parameters['lastModification'] = $now->format(AssetUtil::LAST_MODIFICATION_FORMAT);
        }
        if (!array_key_exists('lastAudit', $parameters)) {
            $parameters['lastAudit'] = $now->format(AssetUtil::LAST_AUDIT_FORMAT);
        }
        if (!array_key_exists('time', $parameters)) {
            $parameters['time'] = $now->format(AssetUtil::TIME_FORMAT);
        }

        // Set nextAudit
        if (!array_key_exists('nextAudit', $parameters)) {
            $nextAuditDateTime = AssetUtil::calculateNextAuditDateTime();
            $parameters['nextAudit'] = $nextAuditDateTime->format(AssetUtil::NEXT_AUDIT_FORMAT);
        }

        return parent::create($parameters);
    }

    /**
     * @inheritDoc
     */
    public function update(array $parameters = [])
    {
        $parameters['lastModification'] = (new DateTime())->format(AssetUtil::LAST_MODIFICATION_FORMAT);

        parent::update($parameters);
    }
}
