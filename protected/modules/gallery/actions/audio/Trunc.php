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
class Trunc extends GalleryAction
{
    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->isModer()) {
            \Yii::app()->message->setErrors('danger', 'У вас нет прав для восстановления этой аудиозаписи');
            \Yii::app()->message->showMessage();
        }

        $id = \Yii::app()->request->getParam('id');
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->addCondition('`t`.is_deleted = 1 or `t`.is_moder_deleted = 1');
        $criteria->scopes = array('truncatedStatus');

        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':id' => $id,
            ':truncatedStatus' => 0
        ));

        /** @var \GalleryAudio $Audio */
        $Audio = \GalleryAudio::model()->find($criteria);
        if(!$Audio)
            \MyException::ShowError(500, 'Аудиозапись не найдена');
        elseif(!$this->isCommunity && \Yii::app()->user->id != $Audio->user_id) {
            \Yii::app()->message->setErrors('danger', 'У вас нет прав для удаления этой аудиозаписи');
            \Yii::app()->message->showMessage();
        }

        $Audio->deleted_trunc = 1;
        if($Audio->mUpdate())
            \Yii::app()->message->setText('success', 'Аудиозапись удалена навсегда');
        else
            \Yii::app()->message->setErrors('danger', 'Возникли проблемы, попробуйте позже');

        \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
        \Yii::app()->message->showMessage();
    }
}