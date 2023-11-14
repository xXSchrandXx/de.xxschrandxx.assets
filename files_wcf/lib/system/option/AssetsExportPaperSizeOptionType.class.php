<?php

namespace wcf\system\option;

use Dompdf\Adapter\CPDF;
use wcf\data\option\Option;
use wcf\system\exception\UserInputException;
use wcf\system\template\ACPTemplateEngine;

class AssetsExportPaperSizeOptionType extends AbstractOptionType
{
    private $validFormats;

    private function getValidFormats()
    {
        if (!isset($this->validFormats)) {
            // load dompdf library
            require_once(ASSETS_DIR . 'lib/system/api/autoload.php');
            $this->validFormats = array_keys(CPDF::$PAPER_SIZES);
        }
        return $this->validFormats;
    }

    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value)
    {
        return ACPTemplateEngine::getInstance()->fetch('assetsExportPaperSizeOptionType', 'assets', [
            'validFormats' => $this->getValidFormats(),
            'option' => $option,
            'value' => $value
        ]);
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue)
    {
        if (empty($newValue)) {
            return;
        }
        if (in_array($newValue, $this->getValidFormats())) {
            return;
        }

        throw new UserInputException($option->optionName);
    }
}
