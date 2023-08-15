<?php

namespace assets\data\location;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\category\CategoryEditor;

class AssetLocationAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = CategoryEditor::class;
}
