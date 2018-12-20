<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 07.06.13
 * Time: 20:14
 * To change this template use File | Settings | File Templates.
 *
 *
 * @var EventItemPost $model
 */?>
<article class="long_block">
    <div class="title"><i class="icon" id="shield"></i><?php echo CHtml::link(
            $model->post->title,
            Yii::app()->createUrl(
                '/post/index/show',
                array('id' => $model->post->id, 'gameId' => $model->user->game_id)
            )); ?></div>
    <div class="content">
        <div class="description">
            <span class="avatar">
                <?php echo CHtml::image($model->user->getAvatar(), '', array('align' => 'left')); ?>
            </span>
            <?php $text = Yii::app()->stringHelper->setBR(Yii::app()->stringHelper->parseTag($model->post->description));
            echo Yii::app()->stringHelper->subStringNew($text, 800, '...');
            ?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="info">
        <div class="left">
            <span class="author"><?php echo $model->user->getFullLogin(); ?></span>
            <time class="time" datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($model->create_datetime)); ?> | <?php echo date('H:i', strtotime($model->create_datetime)); ?></time>
        </div>
        <div class="right">
            <span class="ratingCount ajax"><?php echo $model->post->canRate(); ?>
                <span class="showRate ajax" data-link="<?php echo Yii::app()->createUrl('/post/index/listrate', array('id' => $model->item_id)); ?>" data-html="true" rel="tooltip" title="<?php echo $model->post->getRateList(); ?>">Понравилось
                    <span id="post_<?php echo $model->id; ?>"><?php echo $model->post->rating; ?></span>
                </span>
            </span>
            <a href="<?php echo Yii::app()->createUrl('/post/index/show', array('id' => $model->post->id, 'gameId' => $model->user->game_id)); ?>">
                <span class="commentCount">Комментарии <?php echo $model->post->comment_count; ?></span>
            </a>
        </div>
    </div>
</article>