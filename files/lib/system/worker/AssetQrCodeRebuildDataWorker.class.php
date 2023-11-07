<?php

namespace assets\system\worker;

use assets\data\asset\AssetList;
use wcf\system\worker\AbstractRebuildDataWorker;

class AssetQrCodeRebuildDataWorker extends AbstractRebuildDataWorker
{
    /**
     * @inheritDoc
     */
    protected $objectListClassName = AssetList::class;

    /**
     * @inheritDoc
     */
    protected $limit = 10;

    /**
     * @inheritDoc
     */
    public function execute()
    {
        parent::execute();

        /** @var \assets\data\asset\Asset $asset */
        foreach ($this->objectList as $asset) {
            $path = ASSETS_DIR . 'images/qr/' . $asset->getObjectID() . '.svg';
            if (file_exists($path)) {
                @unlink($path);
            }
            $asset->getQRCode();
        }
    }
}
