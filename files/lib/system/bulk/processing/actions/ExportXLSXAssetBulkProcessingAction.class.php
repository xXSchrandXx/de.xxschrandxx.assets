<?php

namespace assets\system\bulk\processing\actions;

use assets\data\asset\AssetList;
use assets\util\AssetUtil;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use wcf\data\DatabaseObjectList;
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

        // load phpoffice library
        require_once(ASSETS_DIR.'lib/system/api/autoload.php');

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $lang = WCF::getLanguage();

        $activeWorksheet->setCellValue('A1', $lang->getDynamicVariable('assets.acp.asset.bulkProcessing.exportxlsx.header', [
            'now' => (new DateTime('now', AssetUtil::getDateTimeZone()))->format(WCF::getLanguage()->get('wcf.date.dateFormat')),
        ]));

        $activeWorksheet->setCellValue('A2', $lang->get('wcf.global.objectID'));
        $activeWorksheet->setCellValue('B2', $lang->get('wcf.global.title'));
        $activeWorksheet->setCellValue('C2', $lang->get('wcf.page.assetList.category'));
        $activeWorksheet->setCellValue('D2', $lang->get('wcf.page.assetList.amount'));
        $activeWorksheet->setCellValue('E2', $lang->get('wcf.page.assetList.location'));
        $activeWorksheet->setCellValue('F2', $lang->get('wcf.page.assetList.nextAudit'));
        $activeWorksheet->setCellValue('G2', $lang->get('wcf.page.assetList.lastAudit'));
        $activeWorksheet->setCellValue('H2', $lang->get('wcf.page.assetList.lastModification'));
        $activeWorksheet->setCellValue('I2', $lang->get('wcf.page.assetList.time'));

        $row = 3;
        foreach ($objectList->getObjects() as $object) {
            $activeWorksheet->setCellValue('A'.$row, ASSETS_LEGACYID_ENABLED ? $object->getLegacyID() : $object->getObjectID());
            $activeWorksheet->setCellValue('B'.$row, $object->getTitle());
            $activeWorksheet->setCellValue('C'.$row, $object->getCategory()->getTitle());
            $activeWorksheet->setCellValue('D'.$row, $object->getAmount());
            $activeWorksheet->setCellValue('E'.$row, $object->getLocation()->getTitle());
            $activeWorksheet->setCellValue('F'.$row, $object->getNextAuditDateTime()->format(AssetUtil::NEXT_AUDIT_FORMAT));
            $activeWorksheet->setCellValue('G'.$row, $object->getLastAuditDateTime()->format(AssetUtil::LAST_AUDIT_FORMAT));
            $activeWorksheet->setCellValue('H'.$row, $object->getLastModificationDateTime()->format(AssetUtil::LAST_MODIFICATION_FORMAT));
            $activeWorksheet->setCellValue('I'.$row, $object->getCreatedDateTime()->format(AssetUtil::TIME_FORMAT));

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = FileUtil::getTemporaryFilename();
        $writer->save($tempFile);

        $fileReader = new FileReader($tempFile, array(
            'filename' => "file.xlsx",
            'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'filesize' => filesize($tempFile),
            'expirationDate' => TIME_NOW,
            'maxAge' => 0
        ));

        // send file to client
        $fileReader->send();

        @unlink($tempFile);
    }
}
