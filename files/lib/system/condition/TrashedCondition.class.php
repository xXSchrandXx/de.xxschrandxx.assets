<?php

namespace assets\system\condition;

use assets\data\asset\AssetList;
use wcf\data\DatabaseObjectList;
use wcf\system\condition\AbstractCheckboxCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\exception\InvalidObjectArgument;

class TrashedCondition extends AbstractCheckboxCondition implements IObjectListCondition
{
    /**
     * @inheritDoc
     */
    protected $fieldName = 'trashed';

    /**
     * @inheritDoc
     */
    protected $label = 'assets.acp.asset.bulkProcessing.conditionGroup.general.trashed';

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof AssetList)) {
            throw new InvalidObjectArgument($objectList, AssetList::class, 'Object list');
        }

        if ($conditionData[$this->fieldName]) {
            $objectList->getConditionBuilder()->add('asset.isTrashed = 1');
        }
    }
}
