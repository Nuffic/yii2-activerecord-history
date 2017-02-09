<?php

namespace nuffic\activerecord\history\extensions;

/**
*
*/
interface RetrievableHistoryLoggerInterface extends HistoryLoggerInterface
{
    /**
     * @return yii\data\ArrayDataProvider
     */
    public function retrieve($className, $primaryKey);
}
