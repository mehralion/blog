<?php
namespace application\modules\subscribe\actions\request;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class DeleteSubscribe extends \CAction
{
    public function run()
    {
        $model = null;
        $criteria = new \CDbCriteria();
        $criteria->addCondition('subscribe_user_id = :subscribe_user_id');
        $criteria->addCondition('owner_user_id = :owner_user_id');
        $criteria->scopes = array('own');

        if(\Yii::app()->community->id) {
            $criteria->params = array(
                ':subscribe_user_id' => \Yii::app()->user->id,
                ':owner_user_id' => \Yii::app()->community->id
            );
            /** @var \SubscribeCommunity $model */
            $model = \SubscribeCommunity::model()->find($criteria);
        } else {
            $criteria->params = array(
                ':subscribe_user_id' => \Yii::app()->user->id,
                ':owner_user_id' => \Yii::app()->userOwn->id
            );
            /** @var \SubscribeUser $model */
            $model = \SubscribeUser::model()->find($criteria);
        }

        if(!$model)
            \Yii::app()->message->setErrors('danger', 'Подписка не найдена');
        else {
            $model->post = 0;
            $model->image = 0;
            $model->video = 0;
            $model->comment = 0;
            $model->audio = 0;
            $model->update_datetime = \DateTimeFormat::format();
            if($model->save()) {
                \Yii::app()->message->setText('success', 'Вы отписались от подписки');
                \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
            } else
                \Yii::app()->message->setErrors('danger', 'Не удалить отписаться от подписки');
        }

        \Yii::app()->message->showMessage();
    }
}