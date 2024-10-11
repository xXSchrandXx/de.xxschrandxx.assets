<?php

namespace assets\util;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;

class AssetUtil
{
    public const LAST_COMMENT_FORMAT = 'Y-m-d H:i:s'; //DateTimeImmutable::ATOM;
    public const LAST_MODIFICATION_FORMAT = self::LAST_COMMENT_FORMAT;
    public const LAST_AUDIT_FORMAT = self::LAST_COMMENT_FORMAT;
    public const TIME_FORMAT = self::LAST_COMMENT_FORMAT;

    public const NEXT_AUDIT_FORMAT = 'Y-m-d';

    /**
     * Calculates the current datetime for next audit.
     * This does not modify data in database
     * @param ?DateTimeImmutable $lastAudit
     * @return DateTimeImmutable
     * @throws Exception @link(DateTime::__construct()), @link(DateInterval::__construct())
     */
    public static function calculateNextAuditDateTime(?DateTimeImmutable $lastAudit = null): DateTimeImmutable
    {
        $now = new DateTimeImmutable("now", self::getDateTimeZone());
        $interval = new DateInterval('P' . ASSETS_AUDITINTERVAL_AMOUNT . ASSETS_AUDITINTERVAL_UNIT);

        if (ASSETS_AUDIT_INTERVAL) {
            // fix date
            $fixDate = new DateTimeImmutable(ASSETS_AUDIT_DATE, self::getDateTimeZone());

            $nextAuditDateTime = new DateTimeImmutable(
                $now->format("Y") . "-" .
                $fixDate->format("m") . "-" .
                $fixDate->format("d")
            );
            if ($nextAuditDateTime >= $now) {
                $nextAuditDateTime = $nextAuditDateTime->add($interval);
            }
        } else {
            // interval
            if ($lastAudit === null) {
                $lastAudit = $now;
            }

            $nextAuditDateTime = $lastAudit->add($interval);
            while ($nextAuditDateTime <= $now) {
                $nextAuditDateTime = $nextAuditDateTime->add($interval);
            }
        }

        return $nextAuditDateTime;
    }

    /**
     * returns system @link(DateTimeZone)
     * @return DateTimeZone
     */
    public static function getDateTimeZone(): DateTimeZone
    {
        return new DateTimeZone(TIMEZONE);
    }
}
