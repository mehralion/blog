<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 07.06.13
 * Time: 20:14
 * To change this template use File | Settings | File Templates.
 *
 * @var EventItemVideo $model
 */ ?>

<article class="long_block">
    <div class="content">
        <?php echo $model->user->getFullLogin(); ?> добавил видеозапись в альбом
        <span style="font-weight: bold;"><?php echo CHtml::link(
            $model->albumInfo->title,
            Yii::app()->createUrl(
                '/user/show/show_video',
                array('album_id' => $model->albumInfo->id, 'gameId' => $model->user->game_id)
            )); ?>
        </span>
        <div class="preview event_block">
            <?php foreach($model->videoAll as $video): ?>
                <div class="img_border">
                    <?php echo CHtml::link(
                        CHtml::image($video->getImageUrl('small'), $video->title),
                        Yii::app()->createUrl(
                            '/gallery/video/preview',
                            array('id' => $video->id, 'gameId' => $model->user->game_id)),
                        array('title' => $video->title, 'class' => 'preview_video event fancybox-media')
                    ); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="info">
        <div class="left">
            <time class="time" datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($model->create_datetime)); ?> | <?php echo date('H:i', strtotime($model->create_datetime)); ?></time>
        </div>
    </div>
</article>