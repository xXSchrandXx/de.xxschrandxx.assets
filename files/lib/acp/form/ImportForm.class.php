<?php

namespace assets\acp\form;

use PhpOffice\PhpSpreadsheet\IOFactory;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\field\UploadFormField;
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
     * @var \wcf\system\file\upload\UploadFile
     */
    protected $upload;

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
        );
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        parent::validate();

        $this->upload = $this->form->getData()['file'][0];
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractForm::save();

        // TODO
        // load phpoffice library
        require_once(ASSETS_DIR.'lib/system/api/autoload.php');

        $spreadsheet = IOFactory::load(
            $this->upload->getLocation(),
            0,
            [IOFactory::READER_XLSX]
        );
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        wcfDebug($sheetData);

        $this->saved();

        WCF::getTPL()->assign('success', true);
    }
}
