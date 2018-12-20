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
class Subscribe extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.subscribe_user_id = :user_id');
        $criteria->addCondition('`t`.item_id = :owner_id');
        $criteria->params = array(
            ':user_id' => \Yii::app()->user->id,
            ':owner_id' => \Yii::app()->userOwn->id
        );
        $Subscribe = \SubscribeUser::model()->find($criteria);
        if(!$Subscribe) {
            $Subscribe = new \SubscribeUser();
            $Subscribe->create_datetime = \DateTimeFormat::format();
        }
            
        $post = \Yii::app()->request->getParam('SubscribeUser');
        if($post) {
            $Subscribe->attributes = $post;
            $Subscribe->subscribe_user_id = \Yii::app()->user->id;
            $Subscribe->item_id = \Yii::app()->userOwn->id;
            $Subscribe->update_datetime = \DateTimeFormat::format();
            if(!$Subscribe->save())
                \Yii::app()->message->setErrors('danger', 'Возникли проблемы при обновлении подписки');
            else {
                \Yii::app()->message->setText('success', 'Подписка обновлена');
                \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
            }

            \Yii::app()->message->showMessage();
        } else
            $this->controller->renderPartial('subscribe', array('model' => $Subscribe), false, true);
    }
}