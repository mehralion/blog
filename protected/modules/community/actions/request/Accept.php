<?php
namespace application\modules\community\actions\request;
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
class Accept extends CommunityAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->scopes = array('truncatedStatus', 'moderDeletedStatus', 'deletedStatus');
        $criteria->with = array('inRequest' => array('joinType' => 'inner join'));
        $criteria->addCondition('`t`.id = :id');
        $criteria->addCondition('`inRequest`.isInvite = 1');
        $criteria->params = array(':id' => \Yii::app()->community->id, ':truncatedStatus' => 0, ':deletedStatus' => 0, ':moderDeletedStatus' => 0);
        /** @var \Community $model */
        $model = \Community::model()->find($criteria);
        if(!$model) {
            \Yii::app()->message->setErrors('danger', 'Сообщество не найдено');
            \Yii::app()->message->showMessage();
        }
        $Request = $model->inRequest;

        if($model->inCommunity) {
            \Yii::app()->message->setErrors('danger', 'Вы уже состоите в этом сообществе');
            \Yii::app()->message->url = \Yii::app()->createUrl('/community/request/show', array('community_alias' => $model->alias));
            \Yii::app()->message->showMessage();
        }

        $error = false;
        $t = \Yii::app()->db->beginTransaction();

        try {
            $Request->request_status = \CommunityRequest::STATUS_SUCCESS;
            if(!$Request->save()) {
                $error = true;
                \Yii::app()->message->setErrors('danger', $Request);
            }

            $inCommunity = new \CommunityUser();
            $inCommunity->community_id = $model->id;
            $inCommunity->user_id = \Yii::app()->user->id;
            /*if($Request->isModer)
                $inCommunity->user_type = \CommunityUser::TYPE_MODER;
            else*/
            $inCommunity->user_type = \CommunityUser::TYPE_USER;
            $inCommunity->update_datetime = \DateTimeFormat::format();
            $inCommunity->create_datetime = \DateTimeFormat::format();
            if(!$inCommunity->save()) {
                $error = true;
                \Yii::app()->message->setErrors('danger', $inCommunity);
            }

            if(!$error) {
                $t->commit();
                \Yii::app()->message->setText('success', 'Вы успешно вступили в сообщество '.\Yii::app()->community->title);
                \Yii::app()->message->url = \Yii::app()->request->urlReferrer;
            } else
                $t->rollback();

        } catch (\Exception $ex) {
            $t->rollback();
            \MyException::log($ex);
        }

        \Yii::app()->message->showMessage();
    }
}