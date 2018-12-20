<?php
//die('Bad gateway!');
header('Content-Type: text/html; charset=utf-8');
define('YII_DEBUG', false);
$yii=dirname(__FILE__).'/protected/libs/yiisoft/yii/framework/YiiBase.php';
if($_SERVER['SERVER_ADDR']=='88.198.203.220'){
    $config=dirname(__FILE__).'/protected/config/local.php';
} else {
    $config=dirname(__FILE__).'/protected/config/main.php';
}

define('IMAGE_SIZE_MB', 2);

require_once($yii);
/**
 * Class Yii
 *
 */
class Yii extends YiiBase
{
    /**
     * @static
     * @return WebApplication
     */
    public static function app()
    {
        return parent::app();
    }
}

/**
 * Class WebApplication
 *
 * @property Ajax $ajax
 * @property WebUser $user
 * @property UserOwn $userOwn
 * @property Access $access
 * @property Curl $curl
 * @property DGSphinxSearch $search
 * @property ElasticYii $elastic
 * @property Elasticsearch $elasticsearch
 * @property RVS $rvs
 * @property StringHelper $stringHelper
 * @property CImageHandler $ih
 * @property Error $error
 * @property VK $vk
 * @property AWS $aws
 * @property Theme $theme
 * @property CImageComponent $image
 * @property Message $message
 * @property CommunityInfo $community
 * @property ParamsWrap $paramsWrap
 * @property RadioInfo $radio
 */
class WebApplication extends CWebApplication
{

}
/** @var WebApplication $app */
$app = Yii::createWebApplication($config);

spl_autoload_unregister(array('YiiBase', 'autoload'));
require_once Yii::app()->basePath.'/libs/autoload.php';
spl_autoload_register(array('YiiBase', 'autoload'));

$app->run();
