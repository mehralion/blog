<?php
namespace application\modules\gallery\actions\image;
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
    public $deleteLinkRoute = null;
    public $deleteLinkParams = array();
    public $updateLinkRoute = null;
    public $updateLinkParams = array();
    public $linkRoute = null;
    public $linkParams = array();

    public function run()
    {
        if($this->isCommunity) {
            if(!\Yii::app()->community->inCommunity()) {
                \Yii::app()->message->setErrors('danger', 'Вы не состоите в этом сообществе');
                \Yii::app()->message->showMessage();
            }

            $this->deleteLinkRoute = '/community/image/delete';
            $this->updateLinkRoute = '/community/image/update';
            $this->linkRoute = '/community/image/show';

            $this->deleteLinkParams = $this->linkParams = $this->updateLinkParams = array('community_alias' => \Yii::app()->community->alias);
        } else {
            $this->deleteLinkRoute = '/gallery/image/delete';
            $this->updateLinkRoute = '/gallery/image/update';
            $this->linkRoute = '/gallery/image/show';

            $this->updateLinkParams = $this->deleteLinkParams = array('gameId' => \Yii::app()->user->getGameId());
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

        /** @var \GalleryAlbumImage $Album */
        $Album = \GalleryAlbumImage::model()->find($criteria);
        if (!isset($Album)) {
            \Yii::app()->message->setErrors('danger', 'Альбом не найден');
            \Yii::app()->message->showMessage();
        } elseif(!$this->isCommunity && $Album->user_id != \Yii::app()->user->id) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете добавлять аудизаписи в чужой альбом');
            \Yii::app()->message->showMessage();
        }

        /** @var \CUploadedFile[] $uploaders */
        $uploaders = \CUploadedFile::getInstancesByName('GalleryImage');
        foreach ($uploaders as $instance) {
            $t = \Yii::app()->db->beginTransaction();
            $sourceName = $instance->getName();
            $sourcePath = pathinfo($sourceName);

            $Image = new \GalleryImage('create');
            $Image->album_id = $Album->id;
            $Image->file_name = uniqid(md5(time()));
            $Image->file_ext = $sourcePath['extension'];
            $Image->user_id = \Yii::app()->user->id;
            $Image->view_role = $Album->view_role;
            $Image->community_id = $Album->community_id;
            $Image->is_community = $Album->is_community;
            $Image->community_alias = $Album->community_alias;

            if ($Image->create()) {
                $uploader = new \ImageUploader();
                $uploadResult = $uploader->uploadCloud($Image->getBaseUrl(), $Image->file_name.'.'.$Image->file_ext, $instance);
                if($uploadResult) {
                    $t->commit();
                    echo json_encode(array(array(
                        "name" => $instance->getName(),
                        "type" => $instance->getType(),
                        "size" => $instance->getSize(),
                        "url" =>  $Image->getImageUrl('thumbs_small'),
                        "thumbnail_url" => $Image->getImageUrl('thumbs'),
                        'link' => \Yii::app()->createUrl($this->linkRoute,\CMap::mergeArray($this->linkParams, array('id' => $Image->id))),
                        'deleteLink' => \Yii::app()->createUrl($this->deleteLinkRoute, \CMap::mergeArray($this->deleteLinkParams, array('id' => $Image->id))),
                        'updateLink' => \Yii::app()->createUrl($this->updateLinkRoute, \CMap::mergeArray($this->updateLinkParams, array('id' => $Image->id))),
                    )));
                } else
                    throw new \CHttpException(500, "Could not upload file");

            } else
                $t->rollback();
        }
    }
}