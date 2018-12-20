<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 04.06.13
 * Time: 17:51
 * To change this template use File | Settings | File Templates.
 *
 * @package application.components.base
 */
class FrontController extends Controller
{
    public $mainMenu = array();
    public $userMenu = array();
    public $ratingMenu = array();
    public $eventMenu = array();
    public $friendMenu = array();
    public $moderMenu = array();
    public $guestMenu = array();
    public $subscribeMenu = array();
    public $communityMenu = array();

    public $layout = 'column';

    private $_actionCheckSilence = array(
        'update',
        'delete',
        'add',
        'updatelist'
    );

    private $_actionCant = array(
        'update',
        'delete',
        'add',
        'updatelist',
        'comment',
        'image',
        'post',
        'video',
        'user'
    );


    /**
     * @param CAction $action
     * @return bool
     */
    public function beforeAction($action)
    {
        $this->createMenu($action);

        /*if(Yii::app()->user->isGuest && $action->id != 'login')
            $this->redirect(Yii::app()->createUrl('/site/login'));

        if(!Yii::app()->user->isGuest && !Yii::app()->user->isAdmin()) {
            Yii::app()->user->logout();
            $this->redirect(Yii::app()->createUrl('/site/login'));
        }*/

        if(in_array($action->id, $this->_actionCant) && !Yii::app()->user->isGuest && Yii::app()->user->level < Access::CAN_COMMENT)
            MyException::ShowError(404, 'Все возможности доступны с '.Access::CAN_COMMENT.' уровня!');

        if(in_array($action->id, $this->_actionCheckSilence)
            && !Yii::app()->user->isGuest
            && Yii::app()->user->isSilence()
        ) {
            if(Yii::app()->request->isAjaxRequest) {
                $this->renderPartial('ajax.silence');
                Yii::app()->end();
            } else
                MyException::ShowError(404, 'На вас молчанка, для Вас доступен только режим чтения!');
        }

        $this->attachBehavior('menu', array('class' => 'application.behaviors.menu.MenuBehaviors'));
        return parent::beforeAction($action);
    }
    /**
     * @return array
     */
    public function behaviors()
    {
        return CMap::mergeArray(parent::behaviors(), array(
            'menu'=>array(
                'class'=>'application.behaviors.menu.MenuBehaviors',
            ),
        ));
    }


    /**
     * @return string
     */
    public function getViewPath()
    {
        $moduleId = $this->getModule();
        if(null !== $moduleId)
            $moduleId = $moduleId->getId();
        $controllerId = $this->getId();

        $viewPath = Yii::app()->theme->basePath.DIRECTORY_SEPARATOR."views";
        if(null === $moduleId)
            $viewPath .= DIRECTORY_SEPARATOR."www";
        else
            $viewPath .= DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$moduleId;
        $viewPath .= DIRECTORY_SEPARATOR.$controllerId;
        return $viewPath;
    }
}