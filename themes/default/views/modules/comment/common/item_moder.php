<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 06.06.13
 * Time: 16:53
 * To change this template use File | Settings | File Templates.
 *
 * @users CommentItem $model
 * @var CommentItem $model
 */
?>
<style>
    .icon#del {
        margin-top: 5px;
    }
</style>
<article class="long_block">
    <div class="content">
        <?php echo Yii::app()->stringHelper->setBR(Yii::app()->stringHelper->parseTag($model->description)); ?>
        <?php echo $model->drawSubDescriptionsTextDeleted(); ?>
    </div>
    <div class="info">
        <div class="left">
            <span class="author"><?php echo $model->user->getFullLogin(); ?></span>
            <time class="time"
                  datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($model->create_datetime)); ?>
                | <?php echo date('H:i', strtotime($model->create_datetime)); ?></time>
        </div>
    </div>
</article>