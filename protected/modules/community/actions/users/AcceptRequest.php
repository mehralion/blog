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
class AcceptRequest extends CommunityAction
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
        $criteria->addCondition('`t`.isInvite = 0');
        $criteria->params = array(':user_id' => $User->id, ':community_id' => \Yii::app()->community->id, ':requestStatus' => \CommunityRequest::STATUS_PENDING);

        /** @var \CommunityRequest $model */
        $model = \CommunityRequest::model()->find($criteria);
        if(!$model) {
            \Yii::app()->message->setErrors('danger', 'Запрос не найден');
            \Yii::app()->message->showMessage();
        }

        $error = false;
        $t = \Yii::app()->db->beginTransaction();

        try {
            $model->request_status = \CommunityRequest::STATUS_SUCCESS;
            if(!$model->save()) {
                $error = true;
                \Yii::app()->message->setErrors('danger', $model);
            }

            $inCommunity = new \CommunityUser();
            $inCommunity->community_id = $model->community_id;
            $inCommunity->user_id = $User->id;
            $inCommunity->user_type = \CommunityUser::TYPE_USER;
            $inCommunity->update_datetime = \DateTimeFormat::format();
            $inCommunity->create_datetime = \DateTimeFormat::format();
            if(!$inCommunity->save()) {
                $error = true;
                \Yii::app()->message->setErrors('danger', $inCommunity);
            }

            if(!$error) {
                $t->commit();
                \Yii::app()->message->setText('success', 'Пользователь добавлен в участники сообщества');
                \Yii::app()->message->setOther(array('ok' => true));
            } else
                $t->rollback();

        } catch (\Exception $ex) {
            $t->rollback();
            \MyException::log($ex);
        }

        \Yii::app()->message->showMessage();
    }
}