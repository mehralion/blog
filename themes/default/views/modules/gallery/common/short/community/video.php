<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 15.06.13
 * Time: 5:17
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryVideo $model
 */ ?>
<article class="short_block">
    <h3 class="title">
        <?php echo $model->user->getFullLogin(); ?>
    </h3>
    <div class="content">
        <figure class="img_border">
            <?php
                echo CHtml::link(
                    CHtml::image($model->getImageUrl('small')),
                    Yii::app()->createUrl('/community/video/show', array(
                        'id' => $model->id,
                        'community_alias' => $model->community_alias
                    ))
                );
            ?>
        </figure>
    </div>
    <div class="info">
        <span class="ratingCount ajax"><?php echo $model->canRate(); ?> <span id="image_<?php echo $model->id; ?>"><?php echo $model->rating; ?></span></span>
        <a href="<?php echo Yii::app()->createUrl('/community/video/show', array('id' => $model->id, 'community_alias' => $model->community_alias)); ?>">
            <span class="commentCount"><?php echo $model->comment_count; ?></span></a>
    </div>
</article>