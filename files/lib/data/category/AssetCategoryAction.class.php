<?php

namespace assets\data\category;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\category\CategoryEditor;

class AssetCategoryAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = CategoryEditor::class;
}
