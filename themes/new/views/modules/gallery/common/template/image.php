<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 10.10.13
 * Time: 19:20
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryImage $model
 */ ?>

<article class="long_block">
    <div class="content">
        <div class="image_block" style="padding: 10px">
            <div class="center" style="text-align: center">
                <?php echo CHtml::image($model->getImageUrl('thumbs_big'), $model->title); ?>
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
            <?php if($model->update_datetime > $model->create_datetime): ?>
                <time class="time">Изменено: <?php echo date('d.m.Y H:i', strtotime($model->update_datetime)); ?></time>
            <?php endif; ?>
        </div>
        <div class="right">
            <span class="ratingCount ajax"><?php echo $model->canRate(); ?> <span class="showRate ajax" data-link="<?php echo Yii::app()->createUrl('/gallery/image/listrate', array('id' => $model->id)); ?>" data-html="true" rel="tooltip" title="<?php echo $model->getRateList(); ?>">Понравилось <span id="image_<?php echo $model->id; ?>"><?php echo $model->rating; ?></span></span></span>
            <a href="<?php echo Yii::app()->createUrl('/gallery/image/show', array('id' => $model->id, 'gameId' => $model->user->game_id)); ?>"><span class="commentCount">Комментарии <?php echo $model->comment_count; ?></span></a>
        </div>
    </div>
</article>