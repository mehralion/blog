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
class DeleteDebate extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->scopes = array('own', 'deletedStatus');
        $criteria->params = array(':id' => \Yii::app()->request->getParam('id'), ':deletedStatus' => 0);

        /** @var \SubscribeDebate $model */
        $model = \SubscribeDebate::model()->find($criteria);
        if(!$model)
            \Yii::app()->message->setErrors('danger', 'Дискуссия не найдена');
        else {
            $model->is_deleted = 1;
            $model->update_datetime = \DateTimeFormat::format();
            if($model->save())
                \Yii::app()->message->setText('success', 'Вы отписались от подписки');
            else
                \Yii::app()->message->setErrors('danger', 'Не удалить отписаться от подписки');
        }

        \Yii::app()->message->showMessage();
    }
}