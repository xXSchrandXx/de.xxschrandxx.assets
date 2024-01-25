<?php

namespace assets\acp\form;

use assets\data\option\AssetOption;
use assets\data\option\AssetOptionAction;
use assets\data\option\AssetOptionEditor;
use wcf\acp\form\AbstractCustomOptionForm;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

class AssetOptionAddForm extends AbstractCustomOptionForm
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.assets.canManageOption'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.application.assets.option.add';

    /**
     * @inheritDoc
     */
    public $actionClass = AssetOptionAction::class;

    /**
     * @inheritDoc
     */
    public $baseClass = AssetOption::class;

    /**
     * @inheritDoc
     */
    public $editorClass = AssetOptionEditor::class;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        $this->getI18nValue('optionTitle')->setLanguageItem(
            'assets.asset.option',
            'assets.asset',
            'de.xxschrandxx.assets'
        );
        $this->getI18nValue('optionDescription')->setLanguageItem(
            'assets.asset.optionDescription',
            'assets.asset',
            'de.xxschrandxx.assets'
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        parent::save();

        WCF::getTPL()->assign([
            'objectEditLink' => LinkHandler::getInstance()->getControllerLink(
                AssetOptionEditForm::class,
                ['id' => $this->objectAction->getReturnValues()['returnValues']->getObjectID()]
            ),
        ]);
    }
}
