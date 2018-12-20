<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 04.07.13
 * Time: 13:33
 * To change this template use File | Settings | File Templates.
 *
 *
 * @var GalleryImage $model
 */ ?>

<article class="long_block">
    <div class="content">
        <div class="image_block" style="padding: 10px">
            <div class="center" style="text-align: center">
                <?php echo CHtml::image($model->getImageUrl(false), $model->title) ?>
            </div>
            <div class="clear"></div>
            <div class="item_title"><?php echo $model->title; ?></div>
            <div class="item_description">
                <?php echo $model->description; ?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="info">
        <span class="author"><?php echo $model->user->getFullLogin(); ?></span>
        <time class="time"
              datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($model->create_datetime)); ?>
            | <?php echo date('H:i', strtotime($model->create_datetime)); ?></time>
        <span class="ratingCount ajax"><span class="icon" id="like"></span> Понравилось <?php echo $model->rating; ?></span>
        <span class="commentCount">Комментарии <?php echo $model->comment_count; ?></span>
    </div>
</article>