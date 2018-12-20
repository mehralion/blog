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
 * @property integer $user_id
 * @property string $alias
 * @property string $title
 * @property string $description
 * @property string $image
 * @property integer $view_role
 * @property string $update_datetime
 * @property string $create_datetime
 * @property integer $is_deleted
 * @property integer $is_moder_deleted
 * @property integer $step
 * @property integer $rating
 *
 * @package application.components.base
 */
class CommunityInfo extends CApplicationComponent
{
    private $vars = array(
        'id' => false,
        'alias' => false
    );
    /** @var Community */
    private $model = null;

    public $moders = array();

    /**
     * @return bool|void
     */
    public function init()
    {
        parent::init();

        if(null !== $this->model)
            return true;

        $communityAlias = Yii::app()->request->getParam('community_alias');
        if(empty($communityAlias))
            return false;

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{cache_event_item}} where item_type = :item_type');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_COMMUNITY);
        $dependency->reuseDependentData = true;

        $criteria = new CDbCriteria();
        $criteria->scopes = array('truncatedStatus');
        $criteria->addCondition('`t`.alias = :alias');
        $criteria->with = array('inCommunity' => array('joinType' => 'left join'), 'user', 'inRequest', 'canRate');
        $criteria->params = array(':alias' => $communityAlias, ':truncatedStatus' => 0);
        $this->model = Community::model()->cache(0, $dependency)->find($criteria);

        if(null === $this->model)
            MyException::ShowError(404, 'Сообщество не найдено');

        foreach($this->model->attributes as $name => $attr)
            $this->vars[$name] = $attr;

        $criteria = new CDbCriteria();
        $criteria->scopes = array('deletedStatus', 'moders');
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->params = array(':community_id' => $this->model->id, ':deletedStatus' => 0);

        /** @var CommunityUser[] $Moders */
        $Moders = CommunityUser::model()->findAll($criteria);
        foreach($Moders as $Moder)
            $this->moders[] = $Moder->user_id;

        Yii::app()->controller->pageHead .= ' - Сообщество - '.$this->title;
    }

    /**
     * @return bool
     */
    public function isModer()
    {
        return in_array(Yii::app()->user->id, $this->moders);
    }

    /**
     * @return Community
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return bool
     */
    public function inCommunity()
    {
        return isset($this->model->inCommunity);
    }

    /**
     * @return bool
     */
    public function inRequest()
    {
        return isset($this->model->inRequest) && !$this->model->inRequest->isInvite;
    }

    /**
     * @return bool
     */
    public function inInvite()
    {
        return isset($this->model->inRequest) && $this->model->inRequest->isInvite;
    }

    public function isPublic()
    {
        return $this->model->view_role == Community::TYPE_PUBLIC ? true : false;
    }

    /**
     * @return string
     */
    public function getAdministrator()
    {
        return $this->model->user->getFullLogin();
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
}