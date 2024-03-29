<?php

namespace yiiunit\nuffic\activerecord\history\migrations;

use nuffic\activerecord\history\extensions\DbHistoryLogger;
use Yii;
use yii\helpers\ArrayHelper;
use yiiunit\nuffic\activerecord\history\DatabaseTestCase;

/**
*
*/
abstract class BaseMigrationTest extends DatabaseTestCase
{
    public function testMigrationRunner()
    {
        /**
         * @var yii\db\Connection
         */

        unset($this->database['fixture']);
        $connection = self::getConnection();

        $app = $this->mockApplication([
            'components' => [
                'arHistory' => [
                    'class' => DbHistoryLogger::className(),
                    'db' => $connection
                ]
            ]
        ]);

        $controller = new EchoMigrateController('hue', $app, [
            'db' => $connection,
            'interactive' => false,
            'migrationPath' => [
                '@nuffic/activerecord/history/migrations/',
            ]
        ]);
        $controller->runAction('up');

        $this->assertContains('history', $connection->getSchema()->getTableNames());
    }
}
