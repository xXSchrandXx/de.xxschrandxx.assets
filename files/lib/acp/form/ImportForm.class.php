<?php

namespace assets\acp\form;

use assets\data\asset\AssetAction;
use assets\data\category\AssetCategory;
use assets\data\location\AssetLocation;
use assets\util\AssetUtil;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\IOFactory;
use wcf\data\category\CategoryList;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\category\CategoryHandler;
use wcf\system\event\EventHandler;
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
    public $zip = false;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        $this->zip = extension_loaded('zip');
    }

    /**
     * @inheritDoc
     */
    public function createForm()
    {
        parent::createForm();

        $this->form->appendChild(
            UploadFormField::create('file')
                ->label('assets.acp.form.import.field.file')
                ->description('assets.acp.form.import.field.file.description')
                ->setAcceptableFiles(['.xlsx', 'application/vnd.openxmlformats-officedocument. spreadsheetml.sheet'])
                ->available($this->zip)
                ->minimum(1)
                ->maximum(1)
                ->required()
                ->addValidator(new FormFieldValidator('format', function (UploadFormField $field) {
                    foreach ($field->getValue() as $file) {
                        if ($file->getFilenameExtension() != 'xlsx') {
                            $field->addValidationError(
                                new FormFieldValidationError(
                                    'fileExtension',
                                    'assets.acp.form.import.file.error.fileExtension'
                                )
                            );
                        }
                    }
                }))
        );

        $this->form->addDefaultButton($this->zip);
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractForm::save();

        if (!$this->zip) {
            return;
        }

        // load phpoffice library
        require_once(ASSETS_DIR . 'lib/system/api/autoload.php');

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
                    'assets.acp.form.import.file.error.columnID'
                );
            }

            $columnTitle = array_search($lang->get('wcf.global.title'), $header);
            if (!$columnTitle) {
                throw new UserInputException(
                    'file',
                    'assets.acp.form.import.file.error.columnTitle'
                );
            }
            $columnCategoryID = array_search($lang->get('assets.acp.export.categoryID'), $header);
            $columnCategory = array_search($lang->get('assets.acp.export.category'), $header);
            if (!$columnCategoryID && !$columnCategory) {
                throw new UserInputException(
                    'file',
                    'assets.acp.form.import.file.error.columnCategoryID'
                );
            }
            $categoryObjectType = $categoryHandler->getObjectTypeByName('de.xxschrandxx.assets.category');
            $categoryList = new CategoryList();
            $categoryList->getConditionBuilder()->add('objectTypeID = ?', [$categoryObjectType->getObjectID()]);
            $categoryList->readObjects();
            $categories = $categoryList->getObjects();

            $columnLocationID = array_search($lang->get('assets.acp.export.locationID'), $header);
            $columnLocation = array_search($lang->get('assets.acp.export.location'), $header);
            if (!$columnLocationID && !$columnLocation) {
                throw new UserInputException(
                    'file',
                    'assets.acp.form.import.file.error.columnLocationID'
                );
            }
            $locationObjectType = $categoryHandler->getObjectTypeByName('de.xxschrandxx.assets.location');
            $locationList = new CategoryList();
            $locationList->getConditionBuilder()->add('objectTypeID = ?', [$locationObjectType->getObjectID()]);
            $locationList->readObjects();
            $locations = $locationList->getObjects();

            $columnAmount = array_search($lang->get('assets.acp.export.amount'), $header);
            $columnNextAudit = array_search($lang->get('assets.acp.export.nextAudit'), $header);
            $columnLastAudit = array_search($lang->get('assets.acp.export.lastAudit'), $header);
            $columnLastModification = array_search($lang->get('assets.acp.export.lastModification'), $header);
            $columnTime = array_search($lang->get('assets.acp.export.time'), $header);
            $columnDescription = array_search($lang->get('wcf.global.description'), $header);

            EventHandler::getInstance()->fireAction($this, 'checkColumns', $header);

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
            if ($columnAmount && $rowData[$columnAmount] !== null) {
                $parameters['amount'] = $rowData[$columnAmount];
            } else {
                $parameters['amount'] = 1;
            }

            // Set nextAudit
            if ($columnNextAudit && $rowData[$columnNextAudit] !== null) {
                $parameters['nextAudit'] = $rowData[$columnNextAudit];
            }

            // Set lastAudit
            if ($columnLastAudit && $rowData[$columnLastAudit] !== null) {
                $parameters['lastAudit'] = $rowData[$columnLastAudit];
            }

            // Set lastModification
            if ($columnLastModification && $rowData[$columnLastModification] !== null) {
                $parameters['lastModification'] = $rowData[$columnLastModification];
            }

            // Set time
            if ($columnTime && $rowData[$columnTime] !== null) {
                $parameters['time'] = $rowData[$columnTime];
            }

            // Set description
            if ($columnDescription && $rowData[$columnDescription] !== null) {
                $parameters['description'] = $rowData[$columnDescription];
            }

            EventHandler::getInstance()->fireAction($this, 'finalizeParameters', $parameters);

            $action = new AssetAction([], 'create', ['data' => $parameters]);
            $action->executeAction();
        }

        $this->saved();

        WCF::getTPL()->assign([
            'success' => true,
            'skipped' => $skipped,
            'columnTitle' => $columnTitle
        ]);
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign('zip', $this->zip);
    }
}
