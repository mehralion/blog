<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 07.06.13
 * Time: 20:14
 * To change this template use File | Settings | File Templates.
 *
 *
 * @var EventItemImage $model
 */?>
<article class="long_block">
    <div class="content">
        <?php echo $model->user->getFullLogin(); ?> добавил фотограф<?php echo count($model->imageAll)==0?'ию':'ии'; ?> в альбом
        <span style="font-weight: bold;"><?php echo CHtml::link(
            $model->albumInfo->title,
            Yii::app()->createUrl(
                '/user/show/show_image',
                array('album_id' => $model->albumInfo->id, 'gameId' => $model->user->game_id)
            )); ?></span>

        <div class="preview event_block">
            <?php foreach($model->imageAll as $image): ?>
                <div class="img_border">
                    <?php echo CHtml::link(
                        CHtml::image($image->getImageUrl('thumbs_small'), $image->title),
                        Yii::app()->createUrl(
                            '/gallery/image/preview',
                            array('id' => $image->id, 'gameId' => $model->user->game_id)),
                        array('title' => $image->title, 'class' => 'event preview_image fancybox-media')
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