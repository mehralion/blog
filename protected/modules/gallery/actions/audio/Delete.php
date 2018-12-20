<?php
namespace application\modules\gallery\actions\audio;
use application\modules\gallery\components\GalleryAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Delete extends GalleryAction
{
    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->inCommunity()) {
            \Yii::app()->message->setErrors('danger', 'Вы не состоите в сообществе этой аудиозаписи');
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
        /** @var \GalleryAudio $Audio */
        $Audio = \GalleryAudio::model()->find($criteria);
        if(!isset($Audio))
            \Yii::app()->message->setErrors('danger', 'Аудиозапись не найдена!');
        elseif(($this->isCommunity && !\Yii::app()->community->isModer() && !$Audio->user_id != \Yii::app()->user->id)
            || (!$this->isCommunity && $Audio->user_id != \Yii::app()->user->id))
            \Yii::app()->message->setErrors('danger', 'Вы не можете удалить эту аудиозапись!');
        else {
            $Audio->is_deleted = 1;
            $Audio->scenario = 'deleteAudio';
            $Audio->user_deleted_id = \Yii::app()->user->id;
            if($Audio->delete())
                \Yii::app()->message->setText('success', 'Аудиозапись удалена!');
            else
                \Yii::app()->message->setErrors('danger', 'Во время удаления возникла ошибка, попробуйте позже!');
        }

        \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
        \Yii::app()->message->showMessage();
    }
}