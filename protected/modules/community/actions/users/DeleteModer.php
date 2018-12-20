<?php
namespace application\modules\community\actions\users;
use application\modules\community\components\CommunityAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class DeleteModer extends CommunityAction
{
    public function run()
    {
        if(!\Yii::app()->community->isModer()) {
            \Yii::app()->message->setErrors('danger', 'Данное действие доступно только для модераторов группы!');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->params = array(':id' => \Yii::app()->request->getParam('user_id'));

        /** @var \User $User */
        $User = \User::model()->find($criteria);
        if(!$User) {
            \Yii::app()->message->setErrors('danger', 'Пользователь не найден');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->scopes = array('deletedStatus');
        $criteria->addCondition('`t`.user_id = :user_id');
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->addCondition('`t`.user_type = :user_type');
        $criteria->params = array(
            ':user_id' => $User->id,
            ':community_id' => \Yii::app()->community->id,
            ':deletedStatus' => 0,
            ':user_type' => \CommunityUser::TYPE_MODER
        );

        /** @var \CommunityUser $model */
        $model = \CommunityUser::model()->find($criteria);
        if(!$model) {
            \Yii::app()->message->setErrors('danger', 'Пользователь '.$User->login.' не состоит в сообществе или не модератор');
            \Yii::app()->message->showMessage();
        }

        $model->user_type = \CommunityUser::TYPE_USER;
        if($model->save()) {
            \Yii::app()->message->setText('success', 'Участник '.$User->login.' больше не является модератором');
            \Yii::app()->message->setOther(array('ok' => true));
        } else
            \Yii::app()->message->setErrors('danger', $model);

        \Yii::app()->message->showMessage();
    }
}