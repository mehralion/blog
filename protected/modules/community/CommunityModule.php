<?php

class CommunityModule extends CWebModule
{
    public $controllerNamespace = '\application\modules\community\controllers';

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'community.models.*',
			'community.components.*',
			//'community.behaviors.*',
		));
	}

    private $adminController = array('users', 'trunc');

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
            if(!Yii::app()->community->isModer() && in_array($controller->id, $this->adminController)) {
                Yii::app()->message->setErrors('danger', 'Вы не являетесь модератором сообщества');
                Yii::app()->message->url = Yii::app()->createUrl('/community/request/show', array('community_alias' => Yii::app()->community->alias));
                Yii::app()->message->showMessage();
            }
			// this method is called before any module controller action is performed
			// you may place customized code here
            Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/community.js');

            $controller->community = true;
			return true;
		}
		else
			return false;
	}
}
