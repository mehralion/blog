<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 07.06.13
 * Time: 20:14
 * To change this template use File | Settings | File Templates.
 *
 * @var EventCommentAudio $model
 */?>
<article class="long_block">
    <div class="content event_comment">
        <?php echo $model->user->getFullLogin(); ?> добавил комментарий к аудиоальбому
        <?php echo $model->userOwner->getFullLogin(); ?>
        <?php echo CHtml::link(
            $model->info->title,
            Yii::app()->createUrl(
                '/community/album/audio_show',
                array('album_id' => $model->info->album_id, 'comment_id' => $model->comment_id, '#' => 'comment_'.$model->comment_id, 'community_alias' => $model->info->community_alias)
            )); ?>
        <div class="description comment_preview">
            <div class="comment_text text">
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
            <span class="ratingCount ajax"><?php echo $model->comment->canRate(); ?>
                <span class="showRate ajax" data-link="<?php echo Yii::app()->createUrl('/community/listrate/comment_audio', array('id' => $model->comment_id, 'community_alias' => $model->info->community_alias)); ?>" data-html="true" rel="tooltip" title="<?php echo $model->comment->getRateList(); ?>">Понравилось
                    <span id="comment_<?php echo $model->comment->id; ?>"><?php echo $model->comment->rating;  ?></span>
                </span>
            </span>
        </div>
    </div>
</article>