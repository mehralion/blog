<?php
namespace application\modules\gallery\actions\album\image;
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
class Update extends GalleryAction
{
    public $viewName = 'image/form';

    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->inCommunity()) {
            \Yii::app()->message->setErrors('danger', 'Вы не состоите в этом сообществе');
            \Yii::app()->message->showMessage();
        }


        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->scopes = array(
            'own',
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus',
        );
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('album_id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus'=> 0
        );
        /** @var \GalleryAlbumImage $Album */
        $Album = \GalleryAlbumImage::model()->find($criteria);
        if(null === $Album) {
            \Yii::app()->message->setErrors('danger', 'Альбом не найден');
            \Yii::app()->message->showMessage();
        }
        $Album->scenario = 'edit';

        $post = \Yii::app()->request->getParam('GalleryAlbumImage');
        if(!empty($post)) {
            $file = \CUploadedFile::getInstance($Album, 'image_front');
            $Album->attributes = $post;
            $Album->user_update_datetime = \DateTimeFormat::format();
            if(!empty($file)) {
                $Album->is_croped = 0;
                $Album->oldImage = $Album->image_front;
                $Album->image_front = \Yii::app()->user->id.'_'.md5(time()).'.jpg';
            }
            $Album->user_id = \Yii::app()->user->id;

            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                if(!$Album->mUpdate())
                    $error = true;

                if(!$error && !empty($file)) {
                    $old = \Yii::app()->basePath.'/../'.$Album->getBaseUrl() . '/' . $Album->oldImage;
                    if(file_exists($old) && !is_dir($old))
                        unlink($old);
                    \Yii::app()->aws->delete($Album->getBaseUrl() . '/' . $Album->oldImage);

                    $uploader = new \ImageUploader();
                    if(!$uploader->uploadFile($Album->getBaseUrl(), $Album->image_front, $file))
                        $error = true;
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Альбом обновлен');
                } else
                    $t->rollback();

            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
            }

            \Yii::app()->message->showMessage();
        } else
            $this->controller->renderPartial($this->viewName, array(
                'model' => $Album
            ), false, true);
    }
}