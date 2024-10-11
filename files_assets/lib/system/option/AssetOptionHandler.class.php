<?php

namespace assets\system\option;

use assets\data\asset\Asset;
use assets\data\option\AssetOption;
use assets\system\cache\builder\AssetOptionCacheBuilder;
use wcf\system\event\EventHandler;
use wcf\system\option\CustomOptionHandler;

/**
 * @property AssetOption[] $options
 */
class AssetOptionHandler extends CustomOptionHandler
{
    /**
     * @inheritDoc
     */
    protected $cacheClass = AssetOptionCacheBuilder::class;

    /**
     * @inheritDoc
     */
    protected function readCache()
    {
        $cache = \call_user_func([$this->cacheClass, 'getInstance']);

        // get cache contents
        $this->cachedOptions = $cache->getData();

        // allow option manipulation
        EventHandler::getInstance()->fireAction($this, 'afterReadCache');
    }

    /**
     * @var ?Asset
     */
    public $asset;

    public function setAsset(Asset $asset): void
    {
        $this->optionValues = [];
        $this->asset = $asset;

        $this->init();
        foreach ($this->options as &$option) {
            $this->optionValues[$option->optionName] = $this->asset->getOptionValue($option->optionID);
            $option->setOptionValue($this->asset->getOptionValue($option->optionID));
        }
    }
}
