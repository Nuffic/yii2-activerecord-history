<?php

namespace nuffic\activerecord\history\extensions;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use Yii;

/**
* 
*/
class BaseHistoryLogger extends Component
{

    public function init()
    {
        $config = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => "@nuffic/activerecord/history/messages",
            'forceTranslation' => true
        ];
        $globalConfig = ArrayHelper::getValue(Yii::$app->i18n->translations, "arHistory*", []);
        if (!empty($globalConfig)) {
            $config = array_merge($config, is_array($globalConfig) ? $globalConfig : (array) $globalConfig);
        }
        if (!empty($this->i18n) && is_array($this->i18n)) {
            $config = array_merge($config, $this->i18n);
        }
        Yii::$app->i18n->translations["arHistory*"] = $config;
    }

}