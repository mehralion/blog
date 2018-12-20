<?php
namespace application\modules\community\actions\profile;
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
class Settings extends CommunityAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->scopes = array('deletedStatus', 'moderDeletedStatus', 'own', 'truncatedStatus');
        $criteria->addCondition('id = :id');
        $criteria->params = array(
            ':id' => \Yii::app()->community->id,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );
        /** @var \Community $model */
        $model = \Community::model()->find($criteria);
        $model->scenario = 'edit';

        $post = \Yii::app()->request->getParam('Community');
        if($post) {
            $model->attributes = $post;
            if($model->mUpdate()) {
                \Yii::app()->message->url = \Yii::app()->createUrl('/community/request/show', array('community_alias' => $model->alias));
                \Yii::app()->message->setText('success', 'Сообщество сохранено!');
            }

            \Yii::app()->message->showMessage();
        } else {
            $this->controller->render('settings', array(
                'model' => $model
            ));
        }
    }
}