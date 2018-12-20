<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 30.01.14
 * Time: 18:26
 *
 * @var Community $model
 */ ?>
<article class="short_block userSubscribe" id="subscribe_<?php echo $model->id; ?>">
    <h3 class="title">
        <?php echo CHtml::link(
            Yii::app()->stringHelper->subStringNew($model->title, 60, '...'),
            Yii::app()->createUrl('/community/request/show', array('community_alias' => $model->alias)),
            array('rel' => 'tooltip', 'title' => $model->title)
        ); ?>
    </h3>
    <div class="content" style="text-align: left;">
        <figure class="img_border">
            <?php echo CHtml::image($model->getAvatar()); ?>
        </figure>
        <div class="description" style="display: inline-block;width: 125px;">
            <?php $text = \Yii::app()->stringHelper->skipTag($model->description);
            echo Yii::app()->stringHelper->subStringNew($text, 120, '...'); ?>
        </div>
        <div class="clear"></div>
        <?php if(isset($trunc) && $trunc): ?>
            <div class="buttons" style="text-align: center;">
                <?php
                if($model->is_deleted) {
                    echo CHtml::link(
                        '<i class="icon" id="ok" title="Восстановить"></i>',
                        Yii::app()->createUrl('/community/profile/reset', array('community_alias' => $model->alias)),
                        array('title' => 'Восстановить')
                    );

                    echo '<span style="width:10px;display: inline-block"></span>';
                }
                echo CHtml::link(
                    '<i class="icon" id="no" title="Удалить навсегда"></i>',
                    Yii::app()->createUrl('/community/profile/trunc', array('community_alias' => $model->alias)),
                    array('title' => 'Удалить навсегда', 'confirm' => "Вы уверены, что хотите удалить это сообщества навсегда?")
                );
                ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="info">
        <div class="left" style="float: left;">
            <i class="icon" id="subscribePost_active" title="Заметки"></i><span class="ratingCount"><?php echo $model->postCount; ?></span>
            <i class="icon" id="subscribeImage_active" title="Фотографии"></i><span class="ratingCount"><?php echo $model->imageCount; ?></span>
            <i class="icon" id="subscribeVideo_active" title="Видеозаписи"></i><span class="ratingCount"><?php echo $model->videoCount; ?></span>
            <i class="icon" id="subscribeAudio_active" title="Аудиозаписи"></i><span class="ratingCount"><?php echo $model->audioCount; ?></span>
        </div>
        <div class="right">
            <span class="icon" id="like"></span><span class="ratingCount">Понравилось <?php echo $model->rating; ?></span>
        </div>
    </div>
</article>