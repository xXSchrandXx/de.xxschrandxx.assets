<?php

namespace assets\form;

use assets\data\asset\Asset;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;


/**
 * @property   Asset|null    $formObject
 * @property   AssetAction   $objectAction
 */
class AssetEditForm extends AssetAddForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['mod.assets.canModify'];

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['id']) && \is_numeric($_REQUEST['id'])) {
            $this->formObject = new Asset((int)$_REQUEST['id']);
        }

        if (!$this->formObject->getObjectID()) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function checkPermissions()
    {
        if (!$this->formObject->canModify()) {
            throw new PermissionDeniedException();
        }

        parent::checkPermissions();
    }
}
