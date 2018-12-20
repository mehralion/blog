<?php
/**
 * Class RatingModule
 *
 * @package application.rating
 */
class RatingModule extends CWebModule
{
    public $controllerNamespace = '\application\modules\rating\controllers';
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'rating.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
            //\Yii::app()->ajax->setErrors('Временно недоступно!');
            //\Yii::app()->ajax->showMessage();
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
