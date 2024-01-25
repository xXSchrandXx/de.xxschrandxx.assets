<?php

namespace assets\acp\form;

use wcf\acp\form\AbstractCustomOptionForm;

class AssetOptionEditForm extends AssetOptionAddForm
{
    /**
     * @inheritDoc
     */
    public $action = 'edit';

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractCustomOptionForm::save();
    }
}
