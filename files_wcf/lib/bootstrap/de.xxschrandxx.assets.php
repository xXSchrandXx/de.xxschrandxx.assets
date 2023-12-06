<?php

use wcf\system\event\EventHandler;
use wcf\system\language\preload\event\PreloadPhrasesCollecting;
use wcf\system\worker\event\RebuildWorkerCollecting;

return static function (): void {
    $eventHandler = EventHandler::getInstance();

    // Preload phrases
    $eventHandler->register(PreloadPhrasesCollecting::class, static function (PreloadPhrasesCollecting $event) {
        $event->preload('wcf.dialog.confirmation.audit');
    });

    // Delete qr codes
    $eventHandler->register(RebuildWorkerCollecting::class, static function (RebuildWorkerCollecting $event) {
        $event->register(\assets\system\worker\AssetQrCodeRebuildDataWorker::class, 0);
    });
};
