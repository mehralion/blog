<?php
/**
 * Class PreviewController
 *
 * @todo Ограничить только для модераторов
 *
 * @package application.controllers
 */
class PreviewController extends FrontController
{
    public function actionPost()
    {
        $id = Yii::app()->request->getParam('id');
        /** @var Post $Post */
        $Post = Post::model()->findByPk($id);
        if(!isset($Post))
            MyException::ShowError(404, 'Заметка не существует!');

        $this->renderPartial('themePath.views.modules.post.common.post_inner_view_moder', array(
            'model' => $Post
        ));
    }

    public function actionCommunity()
    {
        $id = Yii::app()->request->getParam('id');
        /** @var Community $Community */
        $Community = Community::model()->findByPk($id);
        if(!isset($Community))
            MyException::ShowError(404, 'Сообщество не существует!');

        $this->renderPartial('themePath.views.modules.community.common.community_inner', array(
            'model' => $Community
        ));
    }

    public function actionImage()
    {
        $id = Yii::app()->request->getParam('id');
        /** @var GalleryImage $Image */
        $Image = GalleryImage::model()->findByPk($id);
        if(!isset($Image))
            MyException::ShowError(404, 'Фотография не существует!');

        $this->renderPartial('themePath.views.modules.gallery.common.report_preview.image', array(
            'model' => $Image
        ));
    }

    public function actionVideo()
    {
        $id = Yii::app()->request->getParam('id');
        /** @vat GalleryVideo $Video */
        $Video = GalleryVideo::model()->findByPk($id);
        if(!isset($Video))
            MyException::ShowError(404, 'Видео не существует!');

        $this->renderPartial('themePath.views.modules.gallery.common.report_preview.video', array(
            'model' => $Video
        ));
    }

    public function actionComment()
    {
        $id = Yii::app()->request->getParam('id');
        /** @var CommentItem $Comment */
        $Comment = CommentItem::model()->findByPk($id);
        if(!isset($Comment))
            MyException::ShowError(404, 'Комментарий не существует!');

        $this->renderPartial('themePath.views.modules.comment.common.item_moder', array(
            'model' => $Comment
        ));
    }

    public function actionAudio()
    {
        $id = Yii::app()->request->getParam('id');
        /** @vat GalleryAlbumAudio $AlbumAudio */
        $AlbumAudio = GalleryAlbumAudio::model()->findByPk($id);
        if(!isset($AlbumAudio))
            MyException::ShowError(404, 'Альбом не существует!');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('album_id = :a_id and user_id = :user_id');
        $criteria->scopes = array(
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus'
        );
        $criteria->params = array(
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':a_id' => $AlbumAudio->id,
            ':user_id' => $AlbumAudio->user_id
        );

        $this->renderPartial('themePath.views.modules.gallery.common.report_preview.audio', array(
            'models' => \GalleryAudio::model()->findAll($criteria),
            'album' => $AlbumAudio
        ));
    }
}
