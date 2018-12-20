<?php
namespace application\modules\gallery\actions\video;
use application\modules\gallery\components\GalleryAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.video
 */
class Delete extends GalleryAction
{
    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->inCommunity()) {
            \Yii::app()->message->setErrors('danger', 'Вы не состоите в сообществе этой видеозаписи');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->scopes = array(
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus'
        );
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );
        /** @var \GalleryVideo $Video */
        $Video = \GalleryVideo::model()->find($criteria);
        if(!isset($Video))
            \Yii::app()->message->setErrors('danger', 'Видео не существует!');
        elseif(($this->isCommunity && !\Yii::app()->community->isModer() && !$Video->user_id != \Yii::app()->user->id)
            || (!$this->isCommunity && $Video->user_id != \Yii::app()->user->id))
            \Yii::app()->message->setErrors('danger', 'Вы не можете удалить эту видеозапись!');
        else {
            $Video->scenario = 'deleteVideo';
            $Video->is_deleted = 1;
            $Video->user_deleted_id = \Yii::app()->user->id;
            if($Video->delete())
                \Yii::app()->message->setText('success', 'Видео удалено!');
            else
                \Yii::app()->message->setErrors('danger', 'Возникла ошибка во время удаления, попробуйте позже!');
        }
        \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
        \Yii::app()->message->showMessage();
    }
}