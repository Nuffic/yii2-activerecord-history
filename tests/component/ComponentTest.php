<?php

namespace yiiunit\nuffic\activerecord\history\component;

use yiiunit\nuffic\activerecord\history\DatabaseTestCase;

use nuffic\activerecord\history\extensions\DbHistoryLogger;
use yii\db\AfterSaveEvent;
use yii\base\ModelEvent;
use yiiunit\nuffic\activerecord\history\data\ar\Car;
use yiiunit\nuffic\activerecord\history\data\ar\ActiveRecord;
use yii\db\Query;

/**
*
*/
class ComponentTest extends DatabaseTestCase
{
    protected $driverName = 'sqlite';

    protected function setUp()
    {
        parent::setUp();
        ActiveRecord::$db = $this->getConnection();
    }

    public function testUpdate()
    {
        $connection = $this->getConnection(false);
        $model = new Car([
            'id' => 1
        ]);
        $event = new AfterSaveEvent([
            'sender' => $model,
            'changedAttributes' => [
                'name'
            ]
        ]);

        $logger = new DbHistoryLogger([
            'db' => $connection
        ]);
        $logger->save($event);

        $query = new Query;
        $this->assertEquals(1, $query->select('*')->from('history')->count('*', $connection));
    }

    public function testDelete()
    {
        $connection = $this->getConnection(false);
        $model = new Car([
            'id' => 1
        ]);
        $event = new ModelEvent([
            'name' => ActiveRecord::EVENT_BEFORE_DELETE,
            'sender' => $model,
        ]);

        $logger = new DbHistoryLogger([
            'db' => $connection
        ]);
        $logger->save($event);

        $query = new Query;
        $this->assertEquals(2, $query->select('*')->from('history')->count('*', $connection));
    }
}
