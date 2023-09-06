<?php

namespace assets\system\condition;

use assets\data\asset\AssetList;
use assets\util\AssetUtil;
use DateTimeImmutable;
use wcf\data\DatabaseObjectList;
use wcf\system\condition\AbstractCheckboxCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\exception\InvalidObjectArgument;

class NeedsAuditCondition extends AbstractCheckboxCondition implements IObjectListCondition
{
    /**
     * @inheritDoc
     */
    protected $fieldName = 'needsAudit';

    /**
     * @inheritDoc
     */
    protected $label = 'assets.acp.asset.bulkProcessing.conditionGroup.audit.needsAudit';

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof AssetList)) {
            throw new InvalidObjectArgument($objectList, AssetList::class, 'Object list');
        }

        if ($conditionData[$this->fieldName]) {
            $now = new DateTimeImmutable("now", AssetUtil::getDateTimeZone());
            $dateString = $now->format(AssetUtil::NEXT_AUDIT_FORMAT);
            $objectList->getConditionBuilder()->add("asset.nextAudit <= '$dateString'",);
        }
    }
}
