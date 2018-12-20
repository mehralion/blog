<?php
namespace application\modules\gallery\actions\album\audio;
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
class Add extends GalleryAction
{
    public $viewName = 'audio/form';

    public function run()
    {
        $Album = new \GalleryAlbumAudio('create');

        if($this->isCommunity) {
            if(!\Yii::app()->community->inCommunity()) {
                \Yii::app()->message->setErrors('danger', 'Вы не состоите в этом сообществе');
                \Yii::app()->message->showMessage();
            }

            $Album->community_id = $this->communityId;
            $Album->is_community = 1;
            $Album->community_alias = \Yii::app()->community->alias;

            $this->successLinkRoute = '/community/album/audio_show';
            $this->successLinkParams = array('community_alias' => \Yii::app()->community->alias);
        } else {
            $this->successLinkRoute = '/gallery/album/show_audio';
            $this->successLinkParams = array('gameId' => \Yii::app()->user->getGameId());
        }

        $post = \Yii::app()->request->getParam('GalleryAlbumAudio');
        if(!empty($post)) {
            $file = \CUploadedFile::getInstance($Album, 'image_front');
            $Album->attributes = $post;
            if(!empty($file)) {
                $Album->is_croped = 0;
                $Album->oldImage = $Album->image_front;
                $Album->image_front = \Yii::app()->user->id.'_'.md5(time()).'.jpg';
            }
            $Album->user_id = \Yii::app()->user->id;

            $error = false;
            //Транзация, если картинка альбома не загрузилась, то и альбом не создаем
            $t = \Yii::app()->db->beginTransaction();
            try {
                if(!$Album->create())
                    $error = true;

                if(!$error && !empty($file)) {
                    $uploader = new \ImageUploader();
                    if(!$uploader->uploadFile($Album->getBaseUrl(), $Album->image_front, $file)) {
                        $error = true;
                        \Yii::app()->message->setErrors('danger', 'Обложка не загружена');
                    }
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Альбом был добавлен');
                    \Yii::app()->message->url = \Yii::app()->createUrl(
                        $this->successLinkRoute,
                        \CMap::mergeArray($this->successLinkParams, array('album_id' => $Album->id))
                    );
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