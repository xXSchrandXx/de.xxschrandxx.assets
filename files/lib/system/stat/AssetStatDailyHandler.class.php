<?php

namespace assets\system\stat;

use wcf\system\stat\AbstractStatDailyHandler;

class AssetStatDailyHandler extends AbstractStatDailyHandler
{
    /**
     * @inheritDoc
     */
    public function getData($date)
    {
        return [
            'counter' => $this->getCounter($date, 'assets' . WCF_N . '_asset', 'time'),
            'total' => $this->getTotal($date, 'assets' . WCF_N . '_asset', 'time'),
        ];
    }
}
