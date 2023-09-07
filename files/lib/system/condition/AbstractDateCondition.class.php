<?php

namespace assets\system\condition;

use wcf\data\condition\Condition;
use wcf\data\DatabaseObject;
use wcf\system\condition\AbstractSingleFieldCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\exception\SystemException;

abstract class AbstractDateCondition extends AbstractSingleFieldCondition implements IObjectListCondition
{
    /**
     * name of the checkbox
     * @var string
     */
    protected $fieldName;

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
}
