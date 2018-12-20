<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 07.06.13
 * Time: 20:14
 * To change this template use File | Settings | File Templates.
 *
 * @var EventItemAudio $model
 */ ?>

<article class="long_block">
    <div class="content">
        <?php echo $model->user->getFullLogin(); ?> добавил аудиозапис<?php echo count($model->audioAll) == 1?'ь':'и'; ?> в альбом <?php echo CHtml::link(
            $model->albumInfo->title,
            Yii::app()->createUrl(
                '/community/album/audio_show',
                array('album_id' => $model->album_id, 'community_alias' => $model->albumInfo->community_alias)),
            array('title' => $model->albumInfo->title, 'class' => 'preview_image')
        ); ?>
        <ul class="audio audio_preview event_block">
            <?php foreach($model->audioAll as $audio): ?>
                <li>
                    <div class="audio_player">
                        <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.audio', array(
                            'model' => $audio
                        )); ?>
                    </div>
                    <div class="audio_title"><?php echo $audio->title; ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="info">
        <div class="left">
            <time class="time" datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($model->create_datetime)); ?> | <?php echo date('H:i', strtotime($model->create_datetime)); ?></time>
        </div>
    </div>
</article>