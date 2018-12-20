<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 11.10.13
 * Time: 18:59
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryAlbumAudio $album
 * @var GalleryAudio[] $models
 */ ?>

<article class="long_block">
    <h3 class="title">
        <i class="icon" id="shield"></i>
        <?php echo Yii::app()->stringHelper->subString($album->title, 70, '...');; ?>
    </h3>

    <div class="content">
        <div id="album_audio">
            <!--<div id="audio_image" class="img_border">
                    <?php// echo $album->getImage(); ?>
                </div>-->
            <ul class="audio">
                <?php foreach ($models as $model): ?>
                    <li>
                        <div class="audio_player">
                            <?php $this->renderPartial('webroot.themes.' . Yii::app()->theme->name . '.views.modules.gallery.common.audio', array(
                                'model' => $model
                            )); ?>
                        </div>
                        <div class="audio_title"><?php echo $model->title; ?></div>
                        <?php if (Access::canEdit($album->user_id)): ?>
                            <div class="buttons">
                                <?php
                                echo CHtml::link(
                                    '<i class="icon" id="edit" title="Редактировать"></i>',
                                    Yii::app()->createUrl('/gallery/audio/update', array('id' => $model->id)),
                                    array('class' => 'edit fancybox.ajax', 'title' => 'Редактировать')
                                );
                                echo '<span style="width:10px;display: inline-block"></span>';
                                echo CHtml::link(
                                    '<i class="icon" id="del" title="Удалить"></i>',
                                    Yii::app()->createUrl('/gallery/audio/delete', array('id' => $model->id)),
                                    array('title' => 'Удалить', 'confirm' => "Вы уверены, что хотите отправить эту аудиозапись в корзину? \n Восстановить аудиозапись можно из корзины в любой момент.")
                                );
                                ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php echo $album->drawSubDescriptionsTextDeleted(); ?>
        </div>
        <?php if (empty($models)): ?>
            <div class="event_empty">Список пуст</div>
        <?php endif; ?>
        <? $this->widget('ext.pagination.Pager', array(
            'internalPageCssClass' => 'btn',
            'pages' => $pages,
            'header' => '',
            'selectedPageCssClass' => 'active',
            'htmlOptions' => array(
                'class' => 'btn-group pagination',
            )
        )); ?>
    </div>
    <div class="info">
        <div class="left">
            <span class="author"><?php echo $album->user->getFullLogin(); ?></span>
            <time class="time"
                  datetime="<?php echo Yii::app()->params['siteTimeFormat'] ?>"><?php echo date('d.m.Y', strtotime($album->create_datetime)); ?>
                | <?php echo date('H:i', strtotime($album->create_datetime)); ?></time>
            <?php if ($album->update_datetime > $album->create_datetime): ?>
                <time class="time">Изменено: <?php echo date('d.m.Y H:i', strtotime($album->update_datetime)); ?></time>
            <?php endif; ?>
        </div>
        <div class="right">
            <span class="ratingCount"><span class="icon" id="like"></span> Понравилось <span id="audio_<?php echo $album->id; ?>"><?php echo $album->rating; ?></span></span>
            <span class="commentCount">Комментарии <?php echo $album->comment_count; ?></span>
        </div>
    </div>
</article>