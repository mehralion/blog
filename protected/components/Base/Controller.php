<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 *
 * @package application.components.base
 */
class Controller extends CController
{
    const ROLE_AUTH = 'Authenticated';
    const ROLE_GUEST = 'Guest';

    /**
     * @var string the default layout for the controller view. Defaults to 'column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = 'main';
    public $menu = array();
    public $pageHead = 'Блоги ОлдБК';
    public $breadcrumbs = array();

    public $friend = true;
    public $guest = true;
    public $user = true;
    public $main = true;
    public $event = false;
    public $subscribe = false;
    public $community = false;
    public $rating = true;
    public $moder = true;

    public function getLayoutFile($layoutName)
    {
        $layout = Yii::app()->theme->basePath.DIRECTORY_SEPARATOR."views"
            .DIRECTORY_SEPARATOR."www".DIRECTORY_SEPARATOR."layouts";
        return $this->resolveViewFile($layoutName, $layout, $this->getViewPath());
    }

    /**
     * @param CAction $action
     * @return bool
     */
    public function beforeAction($action)
    {
		//die('Временно недоступен');
		
        if(defined('YII_DEBUG') && YII_DEBUG) {
            Yii::app()->assetManager->forceCopy = true;
        }

        if (Yii::app()->request->isAjaxRequest) {
            // Extract client script
            $clientScript = Yii::app()->clientScript;
            $clientScript->scriptMap = array(
                'jquery-1.8.2.min.js' => false,
                'jquery.js' => false,
                //'jquery-ui.js' => false,
                //'jquery-ui.min.js' => false,
                'jquery.min.js' => false,
                'jquery.yiigridview.js' => false,
                'bootstrap.css' => false,
                'bootstrap.min.css' => false,
                'jquery-ui-bootstrap.css' => false,
                //'jquery.yiiactiveform.js' => false
            );
        } else {
            Yii::app()->clientScript->registerCoreScript('jquery-1.8.2');
            Yii::app()->clientScript->registerPackage('jgrowl');
            Yii::app()->clientScript->registerPackage('fancy');
            Yii::app()->clientScript->registerPackage('fancyHelpers');
            Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/script.js");
            Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/scrollTo.js");
            Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/moder.js");
            Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/ajax.js");
            Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery.history.js");
            Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/crop.js");
            Yii::app()->clientScript->registerPackage('j-ui');
            Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl.'/css/community.css');
        }

        return parent::beforeAction($action);
    }
}