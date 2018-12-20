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
class DeleteInvite extends CommunityAction
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
        $criteria->scopes = array('requestStatus');
        $criteria->addCondition('`t`.user_id = :user_id');
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->addCondition('`t`.isInvite = 1');
        $criteria->params = array(':user_id' => $User->id, ':community_id' => \Yii::app()->community->id, ':requestStatus' => \CommunityRequest::STATUS_PENDING);

        /** @var \CommunityRequest $model */
        $model = \CommunityRequest::model()->find($criteria);
        if(!$model) {
            \Yii::app()->message->setErrors('danger', 'Инвайт не найден');
            \Yii::app()->message->showMessage();
        }

        $model->request_status = \CommunityRequest::STATUS_CANCEL;
        if(!$model->save())
            \Yii::app()->message->setErrors('danger', $model);
        else {
            \Yii::app()->message->setText('success', 'Инвайт отменем');
            \Yii::app()->message->setOther(array('ok' => true));
        }

        \Yii::app()->message->showMessage();
    }
}