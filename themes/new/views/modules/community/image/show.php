<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 06.06.13
 * Time: 16:38
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryImage[] $images
 * @var GalleryImage $model
 */
$this->breadcrumbs = array(
    Yii::app()->community->title => Yii::app()->createUrl('/community/request/show', array('community_alias' => $model->community_alias)),
    'Фотоальбомы' => Yii::app()->createUrl('/community/album/image', array('community_alias' => $model->community_alias)),
    $model->album->title => array(
        '/community/album/image_show',
        'album_id' => $model->album_id,
        'community_alias' => $model->community_alias
    ),
    $model->title
);
?>
    <article class="long_block">
        <div class="content">
            <ul class="album-slider" style="text-align: center;">
                <?php foreach ($images as $image): ?>
                    <?php
                    $border = '';
                    if ($image->id == $model->id)
                        $border = 'border red';
                    ?>
                    <li class="<?php echo $border; ?>">
                        <?php echo CHtml::link(
                            CHtml::image($image->getImageUrl('thumbs_small'), $image->title, array('id' => 'focused_'.$image->id)),
                            Yii::app()->createUrl('/community/image/show', array('id' => $image->id, 'community_alias' => $model->community_alias))
                        ); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="image_block" style="padding: 10px">
                <div class="center" style="text-align: center">
                    <?php echo CHtml::link(
                        CHtml::image($model->getImageUrl('thumbs_big'), $model->title),
                        $model->getImageUrl(false),
                        array('class' => 'fancybox show_origin')
                    ); ?>
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
                            'link' => Yii::app()->createUrl('/community/image/update', array(
                                    'community_alias' => $model->community_alias,
                                    'id' => $model->id,
                                )),
                            'title' => 'Редактировать',
                            'htmlOptions' => array('class' => 'update item'),
                            'visible' => $model->user_id == Yii::app()->user->id
                        ),
                        array(
                            'icon' => 'report',
                            'link' => Yii::app()->createUrl('/moder/report/image', array(
                                    'community_alias' => $model->community_alias,
                                    'id' => $model->id,
                                )),
                            'title' => 'Пожаловаться',
                            'htmlOptions' => array(),
                            'visible' => !$model->is_reported && !Yii::app()->user->isModer() && !Yii::app()->community->isModer()
                        ),
                        array(
                            'icon' => 'del',
                            'link' => Yii::app()->createUrl('/community/image/delete', array(
                                    'community_alias' => $model->community_alias,
                                    'id' => $model->id,
                                )),
                            'title' => 'Удалить заметку',
                            'htmlOptions' => array('confirm' => 'Вы действительно хотите удалить фотографию в корзину?'),
                            'visible' => Yii::app()->community->isModer()
                        ),
                        array(
                            'icon' => 'del',
                            'link' => Yii::app()->createUrl('/moder/image/delete', array(
                                    'community_alias' => $model->community_alias,
                                    'id' => $model->id,
                                )),
                            'title' => 'Удалить фотографию',
                            'htmlOptions' => array('confirm' => 'Вы действительно хотите удалить фотографию?', 'class' => 'moder_delete fancybox.ajax'),
                            'visible' => Yii::app()->user->isModer() && !Yii::app()->community->isModer()
                        ),
                    )
                )); ?>
                <span class="subscribe ajax"><?php echo $model->canSubscribe(); ?></span>
                <span class="ratingCount ajax"><?php echo $model->canRate(); ?> <span class="showRate ajax" data-link="<?php echo Yii::app()->createUrl('/community/listrate/comment_image', array('id' => $model->id, 'community_alias' => $model->community_alias)); ?>" data-html="true" rel="tooltip" title="<?php echo $model->getRateList(); ?>">Понравилось <span id="image_<?php echo $model->id; ?>"><?php echo $model->rating; ?></span></span></span>
                <span class="commentCount">Комментарии <?php echo $model->comment_count; ?></span>
            </div>
        </div>
    </article>
    <?php $this->widget('application.modules.comment.widgets.commentList.CommentListWidget', array(
        'item_type' => ItemTypes::ITEM_TYPE_IMAGE,
        'model' => $model,
        'url' => Yii::app()->createUrl('/comment/image/add', array('id' => $model->id, 'community_alias' => $model->community_alias))
    )); ?>

<script>
    $(function(){
        $(document.body).on('click', '.show_origin', function(event){
            var $self = $(this);
            $.fancybox({
                openEffect	: 'none',
                closeEffect	: 'none',
                href        : $self.attr('href'),
                autoHeight  : true,
                autoWidth   : true
            });
            event.preventDefault();
        });
        previewFocus(<?php echo CJavaScript::encode($model->id); ?>);
    });
</script>
