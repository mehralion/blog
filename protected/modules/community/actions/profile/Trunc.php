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
class Trunc extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->scopes = array('own', 'truncatedStatus');
        $criteria->addCondition('`t`.is_deleted = 1 or `t`.is_moder_deleted = 1');
        $criteria->addCondition('alias = :alias');
        $criteria->params = array(
            ':alias' => \Yii::app()->community->alias,
            ':truncatedStatus' => 0,
        );

        /** @var \Community $model */
        $model = \Community::model()->find($criteria);
        if(!$model) {
            \Yii::app()->message->setErrors('danger', 'Сообщество не найдено');
            \Yii::app()->message->showMessage();
        }

        $model->deleted_trunc = 1;
        if($model->delete()) {
            \Yii::app()->message->url = \Yii::app()->request->urlReferrer;
            \Yii::app()->message->setText('success', 'Сообщество удалено навсегда!');
        }

        \Yii::app()->message->showMessage();
    }
}