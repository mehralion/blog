<?php
/**
 * Class UserOwn контейнер пользователя-владельца
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 14.06.13
 * Time: 21:13
 * To change this template use File | Settings | File Templates.
 *
 * @property integer $id
 *
 * @package application.components.base
 */
class UserOwn extends CApplicationComponent
{
    private $vars;
    /** @var User */
    private $model;

    /**
     * @return bool|void
     */
    public function init()
    {
        parent::init();
        $gameId = Yii::app()->request->getParam('gameId');
        if(empty($gameId))
            return true;

        $this->model = User::model()->cache(Yii::app()->params['cache']['userOwn'])->find('game_id = :game_id', array(
            ':game_id' => $gameId
        ));
        if(null === $this->model && false !== ApiUser::checkUser(null, null, $gameId))
            $this->model = User::model()->find('game_id = :game_id', array(
                ':game_id' => $gameId
            ));

        if(null === $this->model)
            MyException::ShowError(404, 'Пользователь не найден');
        foreach($this->model->attributes as $name => $attr)
        $this->vars[$name] = $attr;

        Yii::app()->controller->pageHead .= ' - '.$this->login;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get($name) {
        if(isset($this->vars[$name]))
            return $this->vars[$name];
        else
            return null;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        if(null !== $this->model)
            return $this->model->getAvatar();
        else
            return Yii::app()->theme->baseUrl.'/images/'.Yii::app()->params['no_avatar'];
    }

    /**
     * @return string
     */
    public function getFullLogin()
    {
        if(null !== $this->model)
            return $this->model->getFullLogin();
        else
            return $this->model->login;
    }
}