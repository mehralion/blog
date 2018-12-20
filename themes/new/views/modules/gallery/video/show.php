<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 06.06.13
 * Time: 16:38
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryVideo[] $videos
 * @var GalleryVideo $model
 */
$this->breadcrumbs = array(
    'Видеоальбомы' => Yii::app()->createUrl('/user/show/album_video', array(
        'gameId' => $model->user->game_id
    )),
    $model->album->title => array(
        '/user/show/show_video',
        'album_id' => $model->album_id,
        'gameId' => $model->user->game_id
    ),
    $model->title
);
?>
    <article class="long_block">
        <h3 class="title"><?php echo $model->title; ?></h3>

        <div class="content">
            <ul class="album-slider" style="text-align: center;">
                <?php foreach ($videos as $video): ?>
                    <?php
                    $border = '';
                    if ($video->id == $model->id)
                        $border = 'border red';
                    ?>
                    <li class="<?php echo $border; ?>"">
                        <?php echo CHtml::link(
                            CHtml::image($video->getImageUrl('small'), $video->title, array('id' => 'focused_'.$video->id)),
                            Yii::app()->createUrl('/gallery/video/show', array('id' => $video->id, 'gameId' => $video->user->game_id))
                        ); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="video_block" style="padding: 10px">
                <div class="center" style="text-align: center">
                    <?php echo $model->getVideoCode(false); ?>
                </div>
                <div class="clear"></div>
                <div class="item_title"><?php echo $model->title; ?></div>
                <div class="item_description">
                    <?php echo Yii::app()->stringHelper->setBR(Yii::app()->stringHelper->parseTag($model->description)); ?>
                </div>
            </div>
            <?php echo $model->drawSubDescriptionsTextDeleted(); ?>
            <div class="clear"></div>
        </div>
        <div class="info">
            <div class="left">
                <span class="author"><?php echo $model->user->getFullLogin(); ?></span>
                <time class="time"
                      datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($model->create_datetime)); ?>
                    | <?php echo date('H:i', strtotime($model->create_datetime)); ?></time>
                <?php if($model->user_update_datetime > $model->create_datetime): ?>
                    <time class="time">Изменено: <?php echo date('d.m.Y H:i', strtotime($model->user_update_datetime)); ?></time>
                <?php endif; ?>
            </div>
            <div class="right">
                <?php $this->widget('application.widgets.itemButtons.ItemButtonsWidget', array(
                    'model' => $model,
                    'editLink' => Yii::app()->createUrl('/gallery/video/update', array('id' => $model->id, 'gameId' => $model->user->game_id)),
                    'deleteLink' => Yii::app()->createUrl('/gallery/video/delete', array('id' => $model->id)),
                    'reportLink' =>  Yii::app()->createUrl('/moder/report/video', array('id' => $model->id, 'gameId' => $model->user->game_id)),
                    'deleteModerLink' => Yii::app()->createUrl('/moder/video/delete', array('id' => $model->id, 'gameId' => $model->user->game_id)),
                    'deleteText' => "Вы уверены, что хотите отправить этот видеоальбом в корзину? \n Восстановить видеоальбом можно из корзины в любой момент.",
                )); ?>
                <span class="subscribe ajax"><?php echo $model->canSubscribe(); ?></span>
                <span class="ratingCount ajax"><?php echo $model->canRate(); ?> <span class="showRate ajax" data-link="<?php echo Yii::app()->createUrl('/gallery/video/listrate', array('id' => $model->id)); ?>" data-html="true" rel="tooltip" title="<?php echo $model->getRateList(); ?>">Понравилось <span id="video_<?php echo $model->id; ?>"><?php echo $model->rating; ?></span></span></span>
                <span class="commentCount">Комментарии <?php echo $model->comment_count; ?></span>
            </div>
        </div>
    </article>

<?php $this->widget('application.modules.comment.widgets.commentList.CommentListWidget', array(
    'item_type' => ItemTypes::ITEM_TYPE_VIDEO,
    'model' => $model,
    'url' => Yii::app()->createUrl('/comment/video/add', array('id' => $model->id, 'gameId' => $model->user->game_id))
)); ?>
<script>
    $(function(){
        previewFocus(<?php echo CJavaScript::encode($model->id); ?>);
    });
</script>
