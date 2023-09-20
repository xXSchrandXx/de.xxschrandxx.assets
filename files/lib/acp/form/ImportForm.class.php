<?php

namespace assets\acp\form;

use assets\data\asset\AssetAction;
use assets\data\category\AssetCategory;
use assets\data\location\AssetLocation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use wcf\data\category\CategoryList;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\field\UploadFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\WCF;

class ImportForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['mod.assets.canAdd'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.application.assets.import';

    /**
     * @inheritDoc
     */
    protected function createForm()
    {
        parent::createForm();

        $this->form->appendChild(
            UploadFormField::create('file')
                ->label('wcf.acp.form.import.field.file')
                ->description('wcf.acp.form.import.field.file.description')
                ->setAcceptableFiles(['.xlsx', 'application/vnd.openxmlformats-officedocument. spreadsheetml.sheet'])
                ->minimum(1)
                ->maximum(1)
                ->required()
                ->addValidator(new FormFieldValidator('format', function (UploadFormField $field) {
                    foreach ($field->getValue() as $file) {
                        if ($file->getFilenameExtension() != 'xlsx') {
                            $field->addValidationError(
                                new FormFieldValidationError(
                                    'fileExtension',
                                    'wcf.acp.form.import.file.error.fileExtension'
                                )
                            );
                        }
                    }
                }))
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractForm::save();

        // load phpoffice library
        require_once(ASSETS_DIR.'lib/system/api/autoload.php');


        $upload = $this->form->getData()['file'][0];

        $spreadsheet = IOFactory::load(
            $upload->getLocation(),
            0,
            [IOFactory::READER_XLSX]
        );

        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $lang = WCF::getLanguage();
        $categoryHandler = CategoryHandler::getInstance();

        $header = $sheetData[1];
        try {
            $columnID = array_search($lang->get('wcf.global.objectID'), $header);
            if (ASSETS_LEGACYID_ENABLED && !$columnID) {
                throw new UserInputException(
                    'file',
                    'wcf.acp.form.import.file.error.columnID'
                );
            }

            $columnTitle = array_search($lang->get('wcf.global.title'), $header);
            if (!$columnTitle) {
                throw new UserInputException(
                    'file',
                    'wcf.acp.form.import.file.error.columnTitle'
                );
            }
            $columnCategoryID = array_search($lang->get('wcf.acp.export.categoryID'), $header);
            $columnCategory = array_search($lang->get('wcf.acp.export.category'), $header);
            if (!$columnCategoryID && !$columnCategory) {
                throw new UserInputException(
                    'file',
                    'wcf.acp.form.import.file.error.columnCategoryID'
                );
            }
            $categoryObjectType = $categoryHandler->getObjectTypeByName('de.xxschrandxx.assets.category');
            $categoryList = new CategoryList();
            $categoryList->getConditionBuilder()->add('objectTypeID = ?', [$categoryObjectType->getObjectID()]);
            $categoryList->readObjects();
            $categories = $categoryList->getObjects();

            $columnLocationID = array_search($lang->get('wcf.acp.export.locationID'), $header);
            $columnLocation = array_search($lang->get('wcf.acp.export.location'), $header);
            if (!$columnLocationID && !$columnLocation) {
                throw new UserInputException(
                    'file',
                    'wcf.acp.form.import.file.error.columnLocationID'
                );
            }
            $locationObjectType = $categoryHandler->getObjectTypeByName('de.xxschrandxx.assets.location');
            $locationList = new CategoryList();
            $locationList->getConditionBuilder()->add('objectTypeID = ?', [$locationObjectType->getObjectID()]);
            $locationList->readObjects();
            $locations = $locationList->getObjects();
    
            $columnAmount = array_search($lang->get('wcf.acp.export.amount'), $header);
            $columnNextAudit = array_search($lang->get('wcf.acp.export.nextAudit'), $header);
            $columnLastAudit = array_search($lang->get('wcf.acp.export.lastAudit'), $header);
            $columnLastModification = array_search($lang->get('wcf.acp.export.lastModification'), $header);
            $columnTime = array_search($lang->get('wcf.acp.export.time'), $header);
            $columnDescription = array_search($lang->get('wcf.global.description'), $header);
        } catch (UserInputException $e) {
            $this->errorField = $e->getField();
            $this->errorType = $e->getType();
            WCF::getTPL()->assign('importError', true);
            return;
        }

        $skipped = [];

        foreach ($sheetData as $rowID => $rowData) {
            if ($rowID === 1) {
                continue;
            }

            $parameters = [];
            // Set title
            if (!isset($rowData[$columnTitle])) {
                $rowData['reason'] = 'noTitle';
                $skipped[$rowID] = $rowData;
                continue;
            }
            $parameters['title'] = $rowData[$columnTitle];

            // Set category
            $categoryID = null;
            if ($columnCategoryID && AssetCategory::getCategory($rowData[$columnCategoryID]) !== null) {
                $categoryID = $rowData[$columnCategoryID];
            } else {
                foreach ($categories as $category) {
                    if ($category->getTitle() == $rowData[$columnCategory]) {
                        $categoryID = $category->getObjectID();
                        break;
                    }
                }
            }
            if ($categoryID === null) {
                $rowData['reason'] = 'unknownCategory';
                $skipped[$rowID] = $rowData;
                continue;
            }
            $parameters['categoryID'] = $categoryID;

            // Set location
            $locationID = null;
            if ($columnLocationID && AssetLocation::getCategory($rowData[$columnLocationID]) !== null) {
                $locationID = $rowData[$columnLocationID];
            } else {
                foreach ($locations as $location) {
                    if ($location->getTitle() == $rowData[$columnLocation]) {
                        $locationID = $location->getObjectID();
                        break;
                    }
                }
            }
            if ($locationID === null) {
                $rowData['reason'] = 'unknownLocation';
                $skipped[$rowID] = $rowData;
                continue;
            }
            $parameters['locationID'] = $locationID;

            // Set legacyID
            if (ASSETS_LEGACYID_ENABLED && $columnID) {
                $parameters['legacyID'] = $rowData[$columnID];
            }

            // Set amount
            if ($columnAmount) {
                $parameters['amount'] = $rowData[$columnAmount];
            } else {
                $parameters['amount'] = 1;
            }

            // Set nextAudit
            if ($columnNextAudit) {
                $parameters['nextAudit'] = $rowData[$columnNextAudit];
            }

            // Set lastAudit
            if ($columnLastAudit) {
                $parameters['lastAudit'] = $rowData[$columnLastAudit];
            }

            // Set lastModification
            if ($columnLastModification) {
                $parameters['lastModification'] = $rowData[$columnLastModification];
            }

            // Set time
            if ($columnTime) {
                $parameters['time'] = $rowData[$columnTime];
            }

            // Set description
            if ($columnDescription) {
                $parameters['description'] = $rowData[$columnDescription];
            }

            $action = new AssetAction([], 'create', ['data' => $parameters]);
//            $action->executeAction();
        }

        $this->saved();

        WCF::getTPL()->assign([
            'success' => true,
            'skipped' => $skipped,
            'columnTitle' => $columnTitle
        ]);
    }
}