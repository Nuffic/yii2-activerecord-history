<?php

namespace nuffic\activerecord\history\extensions;

use yii\base\Component;
use yii\db\BaseActiveRecord;

class DbHistoryLogger extends Component
{
    public $tableName = 'history';

    public $db = 'db';

    public function save($e)
    {
        if (!($e->sender instanceof BaseActiveRecord)) {
            return;
        }
        #var_dump($this->tableName);
        var_dump($e->changedAttributes);
    }
}