<?php

use wcf\system\event\EventHandler;
use wcf\system\worker\event\RebuildWorkerCollecting;

return static function (): void {
    $eventHandler = EventHandler::getInstance();

    // Delete qr codes
    $eventHandler->register(RebuildWorkerCollecting::class, static function (RebuildWorkerCollecting $event) {
        $event->register(\assets\system\worker\AssetQrCodeRebuildDataWorker::class, 0);
    });
};
