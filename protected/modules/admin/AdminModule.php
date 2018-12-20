<?php

class AdminModule extends CWebModule
{

    public $controllerNamespace = '\application\modules\admin\controllers';
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'admin.models.*',
			'admin.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
        $controllerAccess = array(
            'radio' => Yii::app()->user->access(AccessTypes::RADIO_STATUS)
        );

		if(parent::beforeControllerAction($controller, $action))
		{
            if(Yii::app()->user->isAdmin() || (isset($controllerAccess[$controller->id]) && $controllerAccess[$controller->id])) {
                $controller->layout = 'admin';
                // this method is called before any module controller action is performed
                // you may place customized code here
                return true;
            } else {
                if(!Yii::app()->request->isAjaxRequest)
                    $controller->redirect(Yii::app()->createUrl('/post/index/index'));
                else
                    MyException::ShowError(404, 'Страница не найдена');
            }
		}
		else
			return false;
	}
}
