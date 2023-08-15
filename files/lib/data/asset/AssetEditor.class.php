<?php

namespace assets\data\asset;

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
        $parameters['time'] = TIME_NOW;
        $parameters['lastTimeModified'] = TIME_NOW;
        return parent::create($parameters);
    }

    /**
     * @inheritDoc
     */
    public function update(array $parameters = [])
    {
        $parameters['lastTimeModified'] = TIME_NOW;
        parent::update($parameters);
    }
}
