<?php

namespace assets\system\bulk\processing\actions;

use assets\data\asset\AssetList;
use assets\data\option\AssetOption;
use assets\system\option\AssetOptionHandler;
use assets\util\AssetUtil;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use wcf\data\DatabaseObjectList;
use wcf\system\event\EventHandler;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\WCF;
use wcf\util\FileReader;
use wcf\util\FileUtil;

class ExportXLSXAssetBulkProcessingAction extends AbstractAssetBulkProcessingAction
{
    /**
     * @inheritDoc
     */
    public function executeAction(DatabaseObjectList $objectList)
    {
        if (!($objectList instanceof AssetList)) {
            throw new InvalidObjectArgument($objectList, AssetList::class, 'Object list');
        }

        $objectList->readObjects();

        $optionHandler = new AssetOptionHandler(false);
        $optionHandler->init();

        // load phpoffice library
        require_once(ASSETS_DIR . 'lib/system/api/autoload.php');

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $lang = WCF::getLanguage();

        $data = [];

        // set header
        $data[] = [
            $lang->getDynamicVariable('assets.acp.asset.bulkProcessing.exportxlsx.header', [
                'now' => (new DateTime('now', AssetUtil::getDateTimeZone()))->format(WCF::getLanguage()->get('wcf.date.dateFormat')),
            ])
        ];

        // set top row
        $topRow = [
            $lang->get('wcf.global.objectID'),
            $lang->get('wcf.global.title'),
            $lang->get('assets.acp.export.categoryID'),
            $lang->get('assets.acp.export.category'),
            $lang->get('assets.acp.export.amount'),
            $lang->get('assets.acp.export.locationID'),
            $lang->get('assets.acp.export.location'),
            $lang->get('assets.acp.export.nextAudit'),
            $lang->get('assets.acp.export.lastAudit'),
            $lang->get('assets.acp.export.lastModification'),
            $lang->get('assets.acp.export.time'),
            $lang->get('wcf.global.description')
        ];
        /** @var AssetOption $option */
        foreach ($optionHandler->options as $option) {
            array_push($topRow, $option->getTitle());
        }
        EventHandler::getInstance()->fireAction($this, 'topRow', $topRow);
        $data[] = $topRow;

        // set data rows
        foreach ($objectList->getObjects() as $object) {
            $row = [
                ASSETS_LEGACYID_ENABLED ? $object->getLegacyID() : $object->getObjectID(),
                $object->getTitle(),
                $object->getCategoryID(),
                $object->getCategory()->getTitle(),
                $object->getAmount(),
                $object->getLocationID(),
                $object->getLocation()->getTitle(),
                $object->getNextAuditDateTime()->format(AssetUtil::NEXT_AUDIT_FORMAT),
                $object->getLastAuditDateTime()->format(AssetUtil::LAST_AUDIT_FORMAT),
                $object->getLastModificationDateTime()->format(AssetUtil::LAST_MODIFICATION_FORMAT),
                $object->getCreatedDateTime()->format(AssetUtil::TIME_FORMAT),
                $object->getRawDescription()
            ];
            /** @var AssetOption $option */
            foreach ($optionHandler->options as $option) {
                array_push($row, $object->getOptionValue($option->getObjectID()));
            }
            $eventData = [$object, $row];
            EventHandler::getInstance()->fireAction($this, 'row', $eventData);
            $data[] = $eventData['row'];
        }

        $activeWorksheet->fromArray($data);

        $writer = new Xlsx($spreadsheet);
        $tempFile = FileUtil::getTemporaryFilename();
        $writer->save($tempFile);

        $fileReader = new FileReader($tempFile, [
            'filename' => "file.xlsx",
            'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'filesize' => filesize($tempFile),
            'expirationDate' => TIME_NOW,
            'maxAge' => 0
        ]);

        // send file to client
        $fileReader->send();

        @unlink($tempFile);
    }
}
