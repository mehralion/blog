<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
Yii::setPathOfAlias('bootstrap',dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'booster'.DIRECTORY_SEPARATOR);

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
    'preload'=>array('log', 'bootstrap'),
    'import'=>array(
        'application.helpers.*',
        'application.models.*',
        'application.components.*',
        'application.extensions.*', // giix components
        'ext.giix-components.*', // giix components
        'application.components.Base.*',
        'application.extensions.yii-mail.*',
        'application.behaviors.menu.*',
        'application.behaviors.models.*',
    ),
    'modules'=>array(
        'event' => array(),
        'gallery' => array(),
        'comment' => array(),
        'opros' => array(),
        'post' => array(),
        'user' => array(),
        'friend' => array(),
        'rating' => array(),
        'report' => array(),
        'moder' => array(),
    ),
);