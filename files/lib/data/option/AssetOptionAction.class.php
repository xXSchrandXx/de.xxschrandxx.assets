<?php

namespace assets\data\option;

use assets\data\asset\AssetList;
use wcf\data\custom\option\CustomOptionAction;
use wcf\data\ISortableAction;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * @property    AssetOptionEditor[]   $objects
 * @method      AssetOptionEditor[]   getObjects()
 * @method      AssetOptionEditor     getSingleObject()
 */
class AssetOptionAction extends CustomOptionAction implements ISortableAction
{
    /**
     * @inheritDoc
     */
    protected $className = AssetOptionEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.assets.canManageOption'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.assets.canManageOption'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.assets.canManageOption'];

    /**
     * @inheritDoc
     */
    public function create()
    {
        $option = parent::create();

        $assetList = new AssetList();
        $assetList->readObjects();
        foreach ($assetList->getObjects() as $asset) {
            $asset->setOptionValue($option->getObjectID(), $option->defaultValue);
        }

        return $option;
    }
    

    /**
     * @inheritDoc
     */
    public function validateUpdatePosition()
    {
        WCF::getSession()->checkPermissions($this->permissionsUpdate);

        if (!isset($this->parameters['data']['structure']) || !\is_array($this->parameters['data']['structure'])) {
            throw new UserInputException('structure');
        }

        $optionList = new AssetOptionList();
        $optionList->setObjectIDs($this->parameters['data']['structure'][0]);
        if ($optionList->countObjects() !== \count($this->parameters['data']['structure'][0])) {
            throw new UserInputException('structure');
        }
    }

    /**
     * @inheritDoc
     */
    public function updatePosition()
    {
        $sql = "UPDATE  asset" . WCF_N . "_option
                SET     showOrder = ?
                WHERE   optionID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);

        $showOrder = 1;
        WCF::getDB()->beginTransaction();
        foreach ($this->parameters['data']['structure'][0] as $optionID) {
            $statement->execute([
                $showOrder++,
                $optionID,
            ]);
        }
        WCF::getDB()->commitTransaction();
    }
}
