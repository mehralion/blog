<?php
namespace application\modules\gallery\actions\album\video;
use application\modules\gallery\components\GalleryAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.album
 */
class Crop extends GalleryAction
{
    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->inCommunity()) {
            \Yii::app()->message->setErrors('danger', 'Вы не состоите в этом сообществе');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->scopes = array('own');
        $criteria->addCondition('`t`.id = :id');
        $criteria->params = array(':id' => \Yii::app()->request->getParam('album_id'));
        /** @var \GalleryAlbumVideo $Album */
        $Album = \GalleryAlbumVideo::model()->find($criteria);
        if(null === $Album)
            return;

        $post = \Yii::app()->request->getParam('crop');
        if(!empty($post)) {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $oldFile = $Album->getBaseUrl().'/'.$Album->image_front;
                $name = \Yii::app()->user->id.'_'.md5(time()).'.jpg';

                $Album->is_croped = 1;
                $Album->image_front = $name;
                if(!$Album->mUpdate())
                    $error = true;
                                
                $uploader = new \ImageUploader();
                if(!$uploader->crop($oldFile, $post, $Album->getBaseUrl().'/'.$Album->image_front))
                    $error = true;
                                
                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setOther(array('success' => true));
                    \Yii::app()->message->setText('success', 'Альбом обновлен');
                } else
                    $t->rollback();

            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
            }

            \Yii::app()->message->showMessage();
        }
    }
}