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
class Reset extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->scopes = array('own', 'truncatedStatus', 'moderDeletedStatus', 'deletedStatus');
        $criteria->addCondition('alias = :alias');
        $criteria->params = array(
            ':alias' => \Yii::app()->community->alias,
            ':truncatedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':deletedStatus' => 1
        );

        /** @var \Community $model */
        $model = \Community::model()->find($criteria);
        if(!$model) {
            \Yii::app()->message->setErrors('danger', 'Сообщество не найдено');
            \Yii::app()->message->showMessage();
        }

        if(!$model->restore())
            \Yii::app()->message->setErrors('danger', $model);
        else {
            \Yii::app()->message->url = \Yii::app()->request->urlReferrer;
            \Yii::app()->message->setText('success', 'Сообщество восстановленно!');
        }

        \Yii::app()->message->showMessage();
    }
}