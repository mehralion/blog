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
class DeleteUser extends CommunityAction
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
        $criteria->params = array(':user_id' => $User->id, ':community_id' => \Yii::app()->community->id, ':deletedStatus' => 0);

        /** @var \CommunityUser $model */
        $model = \CommunityUser::model()->find($criteria);
        if(!$model) {
            \Yii::app()->message->setErrors('danger', 'Пользователь '.$User->login.' не является участником данного сообщества');
            \Yii::app()->message->showMessage();
        } elseif($model->user_type == \CommunityUser::TYPE_ADMIN) {
            \Yii::app()->message->setErrors('danger', 'Пользователь '.$User->login.' является администратором группы, его нельзя удалить');
            \Yii::app()->message->showMessage();
        }

        $model->is_deleted = 1;
        $model->update_datetime = \DateTimeFormat::format();
        if(!$model->save())
            \Yii::app()->message->setErrors('danger', $model);
        else {
            \Yii::app()->message->setText('success', 'Участник удален из сообщества');
            \Yii::app()->message->setOther(array('ok' => true));
        }

        \Yii::app()->message->showMessage();
    }
}