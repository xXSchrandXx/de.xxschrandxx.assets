<?php

namespace assets\system\bulk\processing\actions;

use wcf\data\DatabaseObjectList;
use wcf\util\FileReader;
use wcf\util\FileUtil;

class GetLabelBulkProcessingAction extends AbstractAssetBulkProcessingAction
{
    /**
     * @inheritDoc
     */
    public $actionName = 'getLabel';

    /**
     * @inheritDoc
     */
    public function executeAction(DatabaseObjectList $objectList)
    {
        parent::executeAction($objectList);

        $tempFile = FileUtil::getTemporaryFilename();
        file_put_contents($tempFile, $this->assetAction->getReturnValues()['returnValues']);

        $fileReader = new FileReader($tempFile, [
            'filename' => "file.hmtl",
            'mimeType' => 'text/html',
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
