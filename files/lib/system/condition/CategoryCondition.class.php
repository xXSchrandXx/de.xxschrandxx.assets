<?php

namespace assets\system\condition;

use assets\data\asset\AssetList;
use wcf\data\DatabaseObjectList;
use wcf\system\condition\AbstractMultiCategoryCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\exception\InvalidObjectArgument;

class CategoryCondition extends AbstractMultiCategoryCondition implements IObjectListCondition
{
    /**
     * @inheritDoc
     */
    public $objectType = 'de.xxschrandxx.assets.category';

    /**
     * @inheritDoc
     */
    protected $fieldName = 'assetCategoryIDs';

    /**
     * @inheritDoc
     */
    protected $label = 'assets.acp.asset.bulkProcessing.conditionGroup.general.category';

    /**
     * @inheritDoc
     */
    protected $description = 'wcf.global.multiSelect';

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof AssetList)) {
            throw new InvalidObjectArgument($objectList, AssetList::class, 'Object list');
        }

        $objectList->getConditionBuilder()->add('asset.categoryID IN (?)', [$conditionData[$this->fieldName]]);
    }
}
