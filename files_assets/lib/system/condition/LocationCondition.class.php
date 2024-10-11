<?php

namespace assets\system\condition;

use assets\data\asset\AssetList;
use wcf\data\DatabaseObjectList;
use wcf\system\condition\AbstractMultiCategoryCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\exception\InvalidObjectArgument;

class LocationCondition extends AbstractMultiCategoryCondition implements IObjectListCondition
{
    /**
     * @inheritDoc
     */
    public $objectType = 'de.xxschrandxx.assets.location';

    /**
     * @inheritDoc
     */
    protected $fieldName = 'assetLocationIDs';

    /**
     * @inheritDoc
     */
    protected $label = 'assets.acp.asset.bulkProcessing.conditionGroup.general.location';

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

        $objectList->getConditionBuilder()->add('asset.locationID IN (?)', [$conditionData[$this->fieldName]]);
    }
}
