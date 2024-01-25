<?php

namespace assets\form;

use assets\data\asset\Asset;
use assets\data\asset\AssetAction;
use assets\data\asset\AssetList;
use assets\data\category\AssetCategoryNodeTree;
use assets\data\location\AssetLocationNodeTree;
use assets\system\option\AssetOptionHandler;
use assets\util\AssetUtil;
use DateTime;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\wysiwyg\WysiwygFormContainer;
use wcf\system\form\builder\CustomFormNode;
use wcf\system\form\builder\field\DateFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\TemplateFormNode;
use wcf\system\WCF;

/**
 * @property   Asset|null    $formObject
 * @property   AssetAction   $objectAction
 */
class AssetAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['mod.assets.canAdd'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'de.xxschrandxx.assets.AssetAdd';

    /**
     * @inheritDoc
     */
    public $objectActionClass = AssetAction::class;

    /**
     * @inheritDoc
     */
    public $objectEditLinkController = AssetEditForm::class;

    /**
     * @inheritDoc
     */
    public $objectEditLinkApplication = 'assets';

    /**
     * @var ?AssetOptionHandler
     */
    public $optionHandler;

    /**
     * category list
     * @var \RecursiveIteratorIterator
     */
    public $categoryList;

    /**
     * location list
     * @var \RecursiveIteratorIterator
     */
    public $locationList;

    /**
     * @var array
     */
    public $categories;

    /**
     * @var array
     */
    public $locations;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        // set categories
        $this->categories = [
            0 => [
                'label' => WCF::getLanguage()->get('wcf.label.none'),
                'value' => 0,
                'depth' => 0,
                'isSelectable' => 1
            ]
        ];
        $categoryTree = new AssetCategoryNodeTree();
        $this->categoryList = $categoryTree->getIterator();
        foreach ($this->categoryList as $category) {
            /** @var \assets\data\category\AssetCategoryNode $category **/
            \array_push($this->categories, [
                'label' => $category->getDecoratedObject()->getTitle(),
                'value' => $category->getObjectID(),
                'depth' => ($category->getDepth() - 1),
                'isSelectable' => ($this->formAction == 'create') ? $category->getDecoratedObject()->canAdd() : $category->getDecoratedObject()->canModify()
            ]);
        }

        // set locations
        $this->locations = [
            0 => [
                'label' => WCF::getLanguage()->get('wcf.label.none'),
                'value' => 0,
                'depth' => 0,
                'isSelectable' => 1
            ],
        ];
        $locationTree = new AssetLocationNodeTree();
        $this->locationList = $locationTree->getIterator();
        foreach ($this->locationList as $location) {
            /** @var \assets\data\location\AssetLocationNode $location **/
            \array_push($this->locations, [
                'label' => $location->getDecoratedObject()->getTitle(),
                'value' => $location->getObjectID(),
                'depth' => ($location->getDepth() - 1),
                'isSelectable' => $location->getDecoratedObject()->canModify() ? 1 : 0
            ]);
        }

        // set optionhandler
        $this->optionHandler = new AssetOptionHandler(false);
        $this->initOptionHandler();
    }

    protected function initOptionHandler()
    {
        $this->optionHandler->init();
    }

    /**
     * @inheritDoc
     */
    protected function createForm()
    {
        parent::createForm();

        $this->form->appendChildren([
            FormContainer::create('data')
                ->appendChildren([
                    TextFormField::create('legacyID')
                        ->label('assets.form.asset.field.legacyID')
                        ->description('assets.form.asset.field.legacyID.description')
                        ->minimumLength(1)
                        ->available(ASSETS_LEGACYID_ENABLED)
                        ->addValidator(
                            new FormFieldValidator(
                                'checkDuplicate',
                                function (TextFormField $field) {
                                    if (
                                        $this->formAction === 'edit'
                                        && $this->formObject instanceof Asset
                                        && $this->formObject->getLegacyID() === $field->getValue()
                                    ) {
                                        return;
                                    }

                                    $assetList = new AssetList();
                                    $assetList->getConditionBuilder()->add('legacyID = ?', [$field->getValue()]);
                                    if ($assetList->countObjects() > 0) {
                                        $field->addValidationError(
                                            new FormFieldValidationError(
                                                'duplicate',
                                                'assets.form.asset.field.legacyID.error.duplicate'
                                            )
                                        );
                                    }
                                }
                            )
                        ),
                    TitleFormField::create()
                        ->value('Default')
                        ->maximumLength(20)
                        ->required(),
                    SingleSelectionFormField::create('categoryID')
                        ->label('assets.form.asset.field.categoryID')
                        ->options($this->categories, true, false)
                        ->addValidator(
                            new FormFieldValidator(
                                'checkCategoryID',
                                static function (SingleSelectionFormField $field) {
                                    if ($field->getValue() === null || $field->getValue() === '0') {
                                        $field->addValidationError(
                                            new FormFieldValidationError(
                                                'invalidValue',
                                                'wcf.global.form.error.noValidSelection'
                                            )
                                        );
                                    }
                                }
                            )
                        )
                        ->required(),
                    IntegerFormField::create('amount')
                        ->label('assets.form.asset.field.amount')
                        ->minimum(1)
                        ->required(),
                    SingleSelectionFormField::create('locationID')
                        ->label('assets.form.asset.field.locationID')
                        ->options($this->locations, true, false)
                        ->addValidator(
                            new FormFieldValidator(
                                'checkLocationID',
                                static function (SingleSelectionFormField $field) {
                                    if ($field->getValue() === null || $field->getValue() === '0') {
                                        $field->addValidationError(
                                            new FormFieldValidationError(
                                                'invalidValue',
                                                'wcf.global.form.error.noValidSelection'
                                            )
                                        );
                                    }
                                }
                            )
                        )
                        ->required(),
                    DateFormField::create('nextAudit')
                        ->label('assets.form.asset.field.nextAudit')
                        ->available($this->formAction === 'edit')
                        ->saveValueFormat(AssetUtil::NEXT_AUDIT_FORMAT)
                        ->earliestDate((new DateTime("now", isset($this->formObject) ? $this->formObject->getDateTimeZone() : null))->format(AssetUtil::NEXT_AUDIT_FORMAT))
                        ->required()
                ]),
            FormContainer::create('options')
                ->label('assets.form.asset.field.options')
                ->appendChild(
                    TemplateFormNode::create('custom_option')
                        ->templateName('customOptionFieldList')
                        ->variables([
                            'errorType' => $this->errorType,
                            'errorField' => $this->errorField,
                            'options' => $this->optionHandler->getOptions()
                        ])
                ),
            WysiwygFormContainer::create('description')
                ->label('assets.form.asset.field.description')
                ->messageObjectType('de.xxschrandxx.assets.asset')
                ->attachmentData(
                    'de.xxschrandxx.assets.asset.attachment',
                    $this->formAction === 'edit' ? $this->formObject->getObjectID() : 0
                ),
            FormContainer::create('edit')
                ->appendChild(
                    TextFormField::create('reason')
                    ->label('wcf.global.reason.optional')
                    ->value('')
                )
                ->available($this->formAction === 'edit')
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        parent::readFormParameters();

        $this->optionHandler->readUserInput($_POST);
    }

    public function validate()
    {
        parent::validate();

        $optionErrors = $this->optionHandler->validate();
        if (!empty($optionErrors)) {
            foreach ($optionErrors as $field => $type) {
                throw new UserInputException($field, $type);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        $this->additionalFields['options'] = $this->optionHandler->save();

        parent::save();
    }
}
