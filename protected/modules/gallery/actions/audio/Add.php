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
class Add extends GalleryAction
{
    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->inCommunity()) {
            \Yii::app()->message->setErrors('danger', 'Вы не состоите в этом сообществе');
            \Yii::app()->message->showMessage();
        }

        $audio = \Yii::app()->request->getParam('Files', array());

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->scopes = array(
            'own',
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus'
        );
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('album_id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );

        /** @var \GalleryAlbumAudio $Album */
        $Album = \GalleryAlbumAudio::model()->find($criteria);
        if (!isset($Album)) {
            \Yii::app()->message->setErrors('danger', 'Альбом не найден');
            \Yii::app()->message->showMessage();
        } elseif(!$this->isCommunity && $Album->user_id != \Yii::app()->user->id) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете добавлять аудизаписи в чужой альбом');
            \Yii::app()->message->showMessage();
        }

        foreach($audio as $item) {
            $GalleryAudio = new \GalleryAudio('create');
            $GalleryAudio->attributes = $item;
            $GalleryAudio->album_id = $Album->id;
            $GalleryAudio->user_id = \Yii::app()->user->id;
            $GalleryAudio->view_role = $Album->view_role;
            $GalleryAudio->is_community = $Album->is_community;
            $GalleryAudio->community_id = $Album->community_id;
            $GalleryAudio->community_alias = $Album->community_alias;
            if(!$GalleryAudio->create()) {
                \Yii::app()->message->setErrors('danger', $GalleryAudio);
                continue;
            } else
                \Yii::app()->message->setText('success', 'Аудиозапись добавлена');
        }

        \Yii::app()->message->url = \Yii::app()->createUrl('/gallery/album/show_audio', array(
            'album_id' => $Album->id,
            'gameId' => \Yii::app()->user->getGameId()
        ));
        \Yii::app()->message->showMessage();
    }
}