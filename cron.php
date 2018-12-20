<?php
date_default_timezone_set('Europe/Moscow');
ini_set("display_errors", 1);
header('Content-Type: text/html; charset=utf-8');
$webRoot=dirname(__FILE__);

$yii=dirname(__FILE__).'/protected/libs/yiisoft/yii/framework/yii.php';
// change the following paths if necessary
require_once($yii);
$configFile=$webRoot.'/protected/config/cron.php';

// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);

/** @var CConsoleApplication $app */
$app = Yii::createConsoleApplication($configFile);

spl_autoload_unregister(array('YiiBase', 'autoload'));
require_once Yii::app()->basePath.'/libs/autoload.php';
spl_autoload_register(array('YiiBase', 'autoload'));

// создаем и запускаем экземпляр приложения
$app->run();
?>