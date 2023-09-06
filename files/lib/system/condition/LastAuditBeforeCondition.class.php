<?php

namespace assets\system\condition;

use assets\data\asset\AssetList;
use assets\util\AssetUtil;
use DateTimeImmutable;
use wcf\data\condition\Condition;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\system\condition\AbstractSingleFieldCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

class LastAuditBeforeCondition extends AbstractSingleFieldCondition implements IObjectListCondition
{
    /**
     * name of the checkbox
     * @var string
     */
    protected $fieldName = 'lastAuditBefore';

    /**
     * @inheritDoc
     */
    protected $label = 'assets.acp.asset.bulkProcessing.conditionGroup.audit.lastAuditBefore';

    /**
     * @var string
     */
    protected $fieldValue = null;

    /**
     * @inheritDoc
     * @throws  SystemException
     */
    public function __construct(DatabaseObject $object)
    {
        parent::__construct($object);

        if ($this->fieldName === null) {
            throw new SystemException("Field name has not been set.");
        }
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        if ($this->fieldValue) {
            return [$this->fieldName => $this->fieldValue];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getFieldElement()
    {
        return '<input type="date" name="' . $this->fieldName . '" id="' . $this->fieldName . '" pattern="\d{4}-\d{2}-\d{2}">';
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        if (!empty($_POST[$this->fieldName])) {
            $this->fieldValue = $_POST[$this->fieldName];
        } else {
            $this->fieldValue = null;
        }
    }

    /**
     * @inheritDoc
     */
    public function reset()
    {
        $this->fieldValue = null;
    }

    /**
     * @inheritDoc
     */
    public function setData(Condition $condition)
    {
        $this->fieldValue = $condition->{$this->fieldName};
    }

    
    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof AssetList)) {
            throw new InvalidObjectArgument($objectList, AssetList::class, 'Object list');
        }

        if (isset($conditionData[$this->fieldName])) {
            $objectList->getConditionBuilder()->add("asset.lastAudit <= '" . $conditionData[$this->fieldName] . "'");
        }
    }
}
