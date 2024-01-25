<?php

namespace assets\form;

use assets\data\asset\Asset;
use assets\system\option\AssetOptionHandler;
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
        if (isset($_REQUEST['id']) && \is_numeric($_REQUEST['id'])) {
            $this->formObject = new Asset((int)$_REQUEST['id']);
        }

        if (!$this->formObject->getObjectID()) {
            throw new IllegalLinkException();
        }

        parent::readParameters();
    }

    /**
     * @inheritDoc
     */
    protected function initOptionHandler()
    {
        $this->optionHandler->setAsset($this->formObject);
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

    /**
     * @inheritDoc
     */
    public function saved()
    {
        parent::saved();

        /** @var TemplateFormNode */
        $node = $this->form->getNodeById('custom_option');
        $node->variables([
            'errorType' => $this->errorType,
            'errorField' => $this->errorField,
            'options' => $this->optionHandler->getOptions()
        ]);
    }
}
