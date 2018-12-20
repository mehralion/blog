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
 * @package application.gallery.actions.audio
 */
class Update extends GalleryAction
{
    public $viewName = 'form_update';

    public function run()
    {
        if($this->isCommunity) {
            if(!\Yii::app()->community->inCommunity()) {
                \Yii::app()->message->setErrors('danger', 'Вы не состоите в сообществе этой аудиозаписи');
                \Yii::app()->message->showMessage();
            }

            $this->successLinkRoute = '/community/album/audio_show';
            $this->successLinkParams = array('community_alias' => \Yii::app()->community->alias);
        } else {
            $this->successLinkRoute = '/gallery/album/show_audio';
            $this->successLinkParams = array('gameId' => \Yii::app()->user->getGameId());
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->with = array(
            'album' => array(
                'scopes' => array('deletedStatus', 'moderDeletedStatus', 'truncatedStatus'),
                'params' => array(
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0
                )
            )
        );
        $criteria->scopes = array(
            'own',
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus');
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0
        );
        /** @var \GalleryAudio $Audio */
        $Audio = \GalleryAudio::model()->find($criteria);
        if(!isset($Audio)) {
            \Yii::app()->message->setErrors('danger', 'Аудиозапись не найдена');
            \Yii::app()->message->showMessage();
        }

        $Audio->scenario = 'updateAudio';
        $post = \Yii::app()->request->getParam('GalleryAudio');
        if(!empty($post)) {
            if(isset($post['album_id']) && $post['album_id'] != $Audio->album_id) {
                $criteria = new \CDbCriteria();
                $criteria->scopes = array(
                    'own',
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus'
                );
                $criteria->addCondition('`t`.id = :album_id');
                $criteria->params = array(
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':album_id' => $post['album_id'],
                    ':truncatedStatus' => 0
                );
                /** @var \GalleryAlbumAudio $Album */
                $Album = \GalleryAlbumAudio::model()->find($criteria);
                if(!$Album)
                    $post['album_id'] = $Audio->album_id;
                else {
                    $Audio->view_role = $Album->view_role;
                    $Audio->community_alias = $Album->community_alias;
                }
            }

            $Audio->attributes = $post;
            $Audio->user_update_datetime = \DateTimeFormat::format();
            if($Audio->mUpdate()) {
                \Yii::app()->message->setText('success', 'Аудиозапись обновлена');
                \Yii::app()->message->url = \Yii::app()->createUrl(
                    $this->successLinkRoute,
                    \CMap::mergeArray($this->successLinkParams, array('album_id' => $Audio->album_id))
                );
            } else
                \Yii::app()->message->setErrors('danger', $Audio);

            \Yii::app()->message->showMessage();
        } else {
            $this->controller->renderPartial($this->viewName, array(
                'model' => $Audio,
            ), false, true);
        }
    }
}