<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 06.06.13
 * Time: 16:09
 * To change this template use File | Settings | File Templates.
 *
 * @var Post $model
 */
?>
<article class="long_block">
    <h3 class="title">
        <i class="icon shield"></i>
        <?= !$model->admin_text ? Yii::app()->stringHelper->subString($model->title, 90, '...') : $model->title; ?>
    </h3>

    <div class="content">
        <div class="description">
            <table>
                <tr>
                    <td style="vertical-align: top;">
                        <span class="avatar">
                            <?php echo CHtml::image($model->user->getAvatar()); ?>
                        </span>
                    </td>
                    <td style="vertical-align: top;">
                        <div style="display: block;word-wrap: break-word;">
                            <?php if(!$model->admin_text): ?>
                                <?= Yii::app()->stringHelper->setBR(Yii::app()->stringHelper->parseTag($model->description)); ?>
                            <?php else: ?>
                                <?= StringHelper::scheme($model->description); ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
        <?php $tags = $model->tags->getTags(); ?>
        <?php if(!empty($tags)): ?>
        <div class="tag_block dark_block">
            Теги:
            <?php $tagstring = ""; ?>
            <?php foreach($tags as $tag): ?>
                <?php if($tagstring != "") $tagstring.= ', '; $tagstring .= $tag; ?>
            <?php endforeach; ?>
            <?php echo $tagstring; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="info">
        <div class="left">
            <span class="author"><?php echo $model->user->getFullLogin(); ?></span>
            <span class="time"><?php echo date('d.m.Y', strtotime($model->create_datetime)); ?>
                | <?php echo date('H:i', strtotime($model->create_datetime)); ?></span>
            <?php if($model->user_update_datetime > $model->create_datetime): ?>
                <span class="time">Изменено: <?php echo date('d.m.Y H:i', strtotime($model->user_update_datetime)); ?></span>
            <?php endif; ?>
        </div>
        <div class="right">
            <span class="ratingCount"><span class="icon" id="like"></span> <span class="showRate">Понравилось <?php echo $model->rating; ?></span></span>
            <span class="commentCount">Комментарии <?php echo $model->comment_count; ?></span>
        </div>
    </div>
</article>