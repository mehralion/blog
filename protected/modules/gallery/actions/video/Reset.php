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
class Reset extends GalleryAction
{
    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->isModer()) {
            \Yii::app()->message->setErrors('danger', 'У вас нет прав для восстановления этой видеозаписи');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->scopes = array(
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus',
        );
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('id'),
            ':deletedStatus' => 1,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0,
        );
        $criteria->with = array('album');

        /** @var \GalleryVideo $Video */
        $Video = \GalleryVideo::model()
            ->find($criteria);
        if (!isset($Video)) {
            \Yii::app()->message->setErrors('danger', 'Подходящая видеозапись не найдена');
            \Yii::app()->message->showMessage();
        } elseif(!$this->isCommunity && \Yii::app()->user->id != $Video->user_id) {
            \Yii::app()->message->setErrors('danger', 'У вас нет прав для восстановления этой видеозаписи');
            \Yii::app()->message->showMessage();
        }

        $t = \Yii::app()->db->beginTransaction();
        $error = false;
        try {
            //Если удаляли альбом, мы восстанавливаем видео и сам альбом
            if($Video->album->is_deleted) {
                if(!$Video->album->restore())
                    $error = true;
            }

            if(!$Video->restore())
                $error = true;

            if(!$error) {
                $t->commit();
                \Yii::app()->message->setText('success', 'Видеозапись восстановлена');
            } else {
                $t->rollback();
                \Yii::app()->message->setErrors('danger', 'Попробуйте позже');
            }
        } catch (\Exception $ex) {
            $t->rollback();
            \MyException::log($ex);
            \Yii::app()->message->setErrors('danger', 'Попробуйте позже');
        }
        \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
        \Yii::app()->message->showMessage();
    }
}