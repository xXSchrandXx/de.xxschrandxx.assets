<?php

namespace assets\form;

use assets\data\asset\Asset;
use assets\data\asset\AssetAction;
use assets\data\asset\AssetList;
use assets\data\category\AssetCategoryNodeTree;
use assets\data\location\AssetLocationNodeTree;
use assets\util\AssetUtil;
use DateTime;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\wysiwyg\WysiwygFormContainer;
use wcf\system\form\builder\field\DateFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
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
     * @inheritDoc
     */
    protected function createForm()
    {
        parent::createForm();

        // Read Categories
        $categories = [
            0 => [
                'label' => WCF::getLanguage()->get('wcf.label.none'),
                'value' => 0,
                'depth' => 0,
                'isSelectable' => 1
            ]
        ];

        // get categories
        $categoryTree = new AssetCategoryNodeTree();
        $this->categoryList = $categoryTree->getIterator();
        foreach ($this->categoryList as $category) {
            /** @var \assets\data\category\AssetCategoryNode $category **/
            \array_push($categories, [
                'label' => $category->getDecoratedObject()->getTitle(),
                'value' => $category->getObjectID(),
                'depth' => ($category->getDepth() - 1),
                'isSelectable' => ($this->formAction == 'create') ? $category->getDecoratedObject()->canAdd() : $category->getDecoratedObject()->canModify()
            ]);
        }

        // Read Locations
        $locations = [
            0 => [
                'label' => WCF::getLanguage()->get('wcf.label.none'),
                'value' => 0,
                'depth' => 0,
                'isSelectable' => 1
            ],
        ];

        // get locations
        $locationTree = new AssetLocationNodeTree();
        $this->locationList = $locationTree->getIterator();
        foreach ($this->locationList as $location) {
            /** @var \assets\data\location\AssetLocationNode $location **/
            \array_push($locations, [
                'label' => $location->getDecoratedObject()->getTitle(),
                'value' => $location->getObjectID(),
                'depth' => ($location->getDepth() - 1),
                'isSelectable' => $location->getDecoratedObject()->canModify() ? 1 : 0
            ]);
        }

        $this->form->appendChildren([
            FormContainer::create('data')
                ->appendChildren([
                    TextFormField::create('legacyID')
                        ->label('wcf.form.asset.field.legacyID')
                        ->description('wcf.form.asset.field.legacyID.description')
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
                                                'wcf.form.asset.field.legacyID.error.duplicate'
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
                        ->label('wcf.form.asset.field.categoryID')
                        ->options($categories, true, false)
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
                        ->label('wcf.form.asset.field.amount')
                        ->minimum(1)
                        ->required(),
                    SingleSelectionFormField::create('locationID')
                        ->label('wcf.form.asset.field.locationID')
                        ->description('wcf.form.asset.field.locationID.description')
                        ->options($locations, true, false)
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
                        ->label('wcf.form.asset.field.nextAudit')
                        ->available($this->formAction === 'edit')
                        ->saveValueFormat(AssetUtil::NEXT_AUDIT_FORMAT)
                        ->earliestDate((new DateTime("now", isset($this->formObject) ? $this->formObject->getDateTimeZone() : null))->format(AssetUtil::NEXT_AUDIT_FORMAT))
                        ->required()
                ]),
            WysiwygFormContainer::create('description')
                ->label('wcf.form.asset.field.description')
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
}
