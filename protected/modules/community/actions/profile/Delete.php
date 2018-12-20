<?php
namespace application\modules\community\actions\profile;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Delete extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->scopes = array('deletedStatus', 'moderDeletedStatus', 'truncatedStatus');
        $criteria->addCondition('alias = :alias');
        $criteria->params = array(
            ':alias' => \Yii::app()->community->alias,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );

        /** @var \Community $model */
        $model = \Community::model()->find($criteria);
        if(!$model) {
            \Yii::app()->message->setErrors('danger', 'Сообщество не найдено');
            \Yii::app()->message->url = \Yii::app()->request->urlReferrer;
            \Yii::app()->message->showMessage();
        }

        if($model->user_id != \Yii::app()->user->id && !\Yii::app()->user->isModer()) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете удалить это сообщество');
            \Yii::app()->message->showMessage();
        }

        $model->user_deleted_id = \Yii::app()->user->id;
        $model->is_deleted = 1;
        if(!$model->delete())
            \Yii::app()->message->setErrors('danger', $model);
        else {
            \Yii::app()->message->url = \Yii::app()->createUrl('/community/profile/own', array('gameId' => \Yii::app()->user->getGameId()));
            \Yii::app()->message->setText('success', 'Сообщество удалено!');
        }

        \Yii::app()->message->showMessage();
    }
}