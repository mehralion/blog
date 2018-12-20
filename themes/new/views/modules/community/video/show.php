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
    Yii::app()->community->title => Yii::app()->createUrl('/community/request/show', array('community_alias' => Yii::app()->community->alias)),
    'Видеоальбомы' => Yii::app()->createUrl('/community/album/video', array('community_alias' => Yii::app()->community->alias)),
    $model->album->title => array(
        '/community/album/video_show',
        'album_id' => $model->album_id,
        'community_alias' => $model->community_alias
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
                            Yii::app()->createUrl('/community/video/show', array('id' => $video->id, 'community_alias' => $model->community_alias))
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
                <?php $this->widget('application.widgets.buttons.MButtonsWidget', array(
                    'buttons' => array(
                        array(
                            'icon' => 'edit',
                            'link' => Yii::app()->createUrl('/community/video/update', array(
                                    'community_alias' => $model->community_alias,
                                    'id' => $model->id,
                                )),
                            'title' => 'Редактировать',
                            'htmlOptions' => array('class' => 'update item'),
                            'visible' => $model->user_id == Yii::app()->user->id
                        ),
                        array(
                            'icon' => 'report',
                            'link' => Yii::app()->createUrl('/moder/report/video', array(
                                    'community_alias' => $model->community_alias,
                                    'id' => $model->id,
                                )),
                            'title' => 'Пожаловаться',
                            'htmlOptions' => array(),
                            'visible' => !$model->is_reported && !Yii::app()->user->isModer() && !Yii::app()->community->isModer()
                        ),
                        array(
                            'icon' => 'del',
                            'link' => Yii::app()->createUrl('/community/video/delete', array(
                                    'community_alias' => $model->community_alias,
                                    'id' => $model->id,
                                )),
                            'title' => 'Удалить заметку',
                            'htmlOptions' => array('confirm' => 'Вы действительно хотите удалить видеозапись в корзину?'),
                            'visible' => Yii::app()->community->isModer()
                        ),
                        array(
                            'icon' => 'del',
                            'link' => Yii::app()->createUrl('/moder/video/delete', array(
                                    'community_alias' => $model->community_alias,
                                    'id' => $model->id,
                                )),
                            'title' => 'Удалить сообщество',
                            'htmlOptions' => array('confirm' => 'Вы действительно хотите удалить видеозапись?', 'class' => 'moder_delete fancybox.ajax'),
                            'visible' => Yii::app()->user->isModer() && !Yii::app()->community->isModer()
                        ),
                    )
                )); ?>
                <span class="subscribe ajax"><?php echo $model->canSubscribe(); ?></span>
                <span class="ratingCount ajax"><?php echo $model->canRate(); ?> <span class="showRate ajax" data-link="<?php echo Yii::app()->createUrl('/community/listrate/video', array('id' => $model->id, 'community_alias' => $model->community_alias)); ?>" data-html="true" rel="tooltip" title="<?php echo $model->getRateList(); ?>">Понравилось <span id="video_<?php echo $model->id; ?>"><?php echo $model->rating; ?></span></span></span>
                <span class="commentCount">Комментарии <?php echo $model->comment_count; ?></span>
            </div>
        </div>
    </article>

<?php $this->widget('application.modules.comment.widgets.commentList.CommentListWidget', array(
    'item_type' => ItemTypes::ITEM_TYPE_VIDEO,
    'model' => $model,
    'url' => Yii::app()->createUrl('/comment/video/add', array('id' => $model->id, 'community_alias' => $model->community_alias))
)); ?>
<script>
    $(function(){
        previewFocus(<?php echo CJavaScript::encode($model->id); ?>);
    });
</script>
