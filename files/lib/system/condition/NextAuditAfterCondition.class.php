<?php

namespace assets\system\condition;

use assets\data\asset\AssetList;
use wcf\data\DatabaseObjectList;
use wcf\system\exception\InvalidObjectArgument;

class NextAuditAfterCondition extends AbstractDateCondition
{
    /**
     * @inheritDoc
     */
    protected $fieldName = 'nextAuditAfter';

    /**
     * @inheritDoc
     */
    protected $label = 'assets.acp.asset.bulkProcessing.conditionGroup.audit.nextAuditAfter';

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof AssetList)) {
            throw new InvalidObjectArgument($objectList, AssetList::class, 'Object list');
        }

        if (isset($conditionData[$this->fieldName])) {
            $objectList->getConditionBuilder()->add("asset.nextAudit > '" . $conditionData[$this->fieldName] . "'");
        }
    }
}
