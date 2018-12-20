<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 07.06.13
 * Time: 20:14
 * To change this template use File | Settings | File Templates.
 *
 *
 *
 * @var EventCommentImage $model
 */ ?>

<article class="long_block">
    <div class="content event_comment">
        <?php echo $model->user->getFullLogin(); ?> <span style="vertical-align: bottom;">добавил комментарий к фотографии</span>
        <?php echo $model->userOwner->getFullLogin().' '; ?>
        <div class="description comment_preview">
            <div class="img_border">
                <?php echo CHtml::link(
                    CHtml::image(GalleryImage::model()->getImageUrl('thumbs_small', $model->info, $model->user_owner_id), '', array('class' => 'preview')),
                    Yii::app()->createUrl(
                        '/gallery/image/show',
                        array('id' => $model->info->item_id, 'comment_id' => $model->comment_id, '#' => 'comment_'.$model->comment_id, 'gameId' => $model->userOwner->game_id)
                    ),
                    array(
                        'class' => 'preview_image'
                    )); ?>
            </div>
            <div class="comment_text text" style="min-height: 91px;">
                <?php $text = Yii::app()->stringHelper->setBR(Yii::app()->stringHelper->parseTag($model->comment->description));
                echo Yii::app()->stringHelper->subStringNew($text, 200, '...');
                ?>
            </div>
        </div>
    </div>
    <div class="info">
        <div class="left">
            <time class="time" datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($model->create_datetime)); ?> | <?php echo date('H:i', strtotime($model->create_datetime)); ?></time>
        </div>
        <div class="right">
            <span class="ratingCount ajax"><?php echo $model->comment->canRate(); ?> <span class="showRate ajax" data-link="<?php echo Yii::app()->createUrl('/comment/image/listrate', array('id' => $model->comment_id)); ?>" data-html="true" rel="tooltip" title="<?php echo $model->comment->getRateList(); ?>">Понравилось <span id="comment_<?php echo $model->comment->id; ?>"><?php echo $model->comment->rating;  ?></span></span></span>
        </div>
    </div>
</article>