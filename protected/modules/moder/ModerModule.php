<?php
/**
 * Class ModerModule
 *
 * @package application.moder
 */
class ModerModule extends CWebModule
{
    public $controllerNamespace = '\application\modules\moder\controllers';
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'moder.components.*',
		));
	}

    private $userCanModerController = array('report');

    public $postLabel = 'Заметки';
    public $imageLabel = 'Фотографии';
    public $videoLabel = 'Видеозаписи';
    public $audioLabel = 'Аудиозаписи';
    public $commentLabel = 'Комментарии';
    public $communityLabel = 'Сообщества';

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
            if(!Yii::app()->user->isModer() && !in_array($controller->id, $this->userCanModerController)) {
                if(!Yii::app()->request->isAjaxRequest)
                    $controller->redirect(Yii::app()->createUrl('/post/index/index'));
                else
                    MyException::ShowError(404, 'Страница не найдена');
            }

            if(ReportPost::model()->open()->count())
                $this->postLabel .= ' [ <div class="red attention">!</div> ]';
            if(ReportImage::model()->open()->count())
                $this->imageLabel .= ' [ <div class="red attention">!</div> ]';
            if(ReportVideo::model()->open()->count())
                $this->videoLabel .= ' [ <div class="red attention">!</div> ]';
            if(ReportAudioAlbum::model()->open()->count())
                $this->audioLabel .= ' [ <div class="red attention">!</div> ]';
            if(ReportComment::model()->open()->count())
                $this->commentLabel .= ' [ <div class="red attention">!</div> ]';
            if(ReportCommunity::model()->open()->count())
                $this->communityLabel .= ' [ <div class="red attention">!</div> ]';

            $controller->layout = 'moder';
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
