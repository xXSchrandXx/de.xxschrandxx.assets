<?php

namespace assets\system\condition;

use assets\data\asset\AssetList;
use wcf\data\DatabaseObjectList;
use wcf\system\exception\InvalidObjectArgument;

class LastAuditAfterCondition extends AbstractDateCondition
{
    /**
     * @inheritDoc
     */
    protected $fieldName = 'lastAuditAfter';

    /**
     * @inheritDoc
     */
    protected $label = 'assets.acp.asset.bulkProcessing.conditionGroup.audit.lastAuditAfter';

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof AssetList)) {
            throw new InvalidObjectArgument($objectList, AssetList::class, 'Object list');
        }

        if (isset($conditionData[$this->fieldName])) {
            $objectList->getConditionBuilder()->add("asset.lastAudit > '" . $conditionData[$this->fieldName] . "'");
        }
    }
}
