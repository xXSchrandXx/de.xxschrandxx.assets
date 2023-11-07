<?php

namespace assets\system\bulk\processing\actions;

use assets\data\asset\AssetList;
use assets\util\AssetUtil;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use wcf\data\DatabaseObjectList;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\template\ACPTemplateEngine;
use wcf\system\WCF;
use wcf\util\FileReader;
use wcf\util\FileUtil;

class ExportPDFAssetBulkProcessingAction extends AbstractAssetBulkProcessingAction
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
        $objects = $objectList->getObjects();

        // load dompdf library
        require_once(ASSETS_DIR . 'lib/system/api/autoload.php');

        $options = new Options();
        $options->setTempDir(WCF_DIR . 'tmp/');
        $options->setLogOutputFile(WCF_DIR . 'tmp/dompdf.log');
        $options->setIsRemoteEnabled(true);
        $options->setFontDir(WCF_DIR . 'font/families');
        $options->setDefaultPaperSize(ASSETS_EXPORT_FORMAT);
        $options->setDefaultPaperOrientation(ASSETS_EXPORT_ORIENTATION);

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml(ACPTemplateEngine::getInstance()->fetch('__bulkProcessingAssetListExport', 'assets', [
            'objects' => $objects
        ], true));

        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        // add left footer
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = WCF::getLanguage()->getDynamicVariable('assets.acp.asset.bulkProcessing.exportpdf.footer.left', [
                'now' => (new DateTime('now', AssetUtil::getDateTimeZone()))->format(WCF::getLanguage()->get('wcf.date.dateFormat')),
                'pageNumber' => $pageNumber,
                'pageCount' => $pageCount
            ]);
            $font = $fontMetrics->getFont('monospace');
            $pageHeight = $canvas->get_height();
            $size = 12;
            $canvas->text(20, $pageHeight - 20, $text, $font, $size);
        });
        // add right footer
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = WCF::getLanguage()->getDynamicVariable('assets.acp.asset.bulkProcessing.exportpdf.footer.right', [
                'now' => (new DateTime('now', AssetUtil::getDateTimeZone()))->format(WCF::getLanguage()->get('wcf.date.dateFormat')),
                'pageNumber' => $pageNumber,
                'pageCount' => $pageCount
            ]);
            $font = $fontMetrics->getFont('monospace');
            $pageWidth = $canvas->get_width();
            $pageHeight = $canvas->get_height();
            $size = 12;
            $width = $fontMetrics->getTextWidth($text, $font, $size);
            $canvas->text($pageWidth - $width - 20, $pageHeight - 20, $text, $font, $size);
        });

        $tempFile = FileUtil::getTemporaryFilename();
        file_put_contents($tempFile, $dompdf->output());

        $fileReader = new FileReader($tempFile, [
            'filename' => "file.pdf",
            'mimeType' => 'application/pdf',
            'filesize' => filesize($tempFile),
            'showInline' => true,
            'enableRangeSupport' => false,
            'expirationDate' => TIME_NOW,
            'maxAge' => 0
        ]);

        // send file to client
        $fileReader->send();

        @unlink($tempFile);
    }
}
