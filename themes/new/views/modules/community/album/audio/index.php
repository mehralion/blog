<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 14:00
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryAlbumAudio[] $models
 * @var CPagination $pages
 */
$this->breadcrumbs = array(
    Yii::app()->community->title => Yii::app()->createUrl('/community/request/show', array('community_alias' => Yii::app()->community->alias)),
    'Аудиоальбомы'
);
?>
<?php if(Yii::app()->community->inCommunity()): ?>
<div class="buttons">
    <i class="icon" id="album_icon_audio"></i>
    <div class="m_button">
        <?php echo CHtml::link('Добавить аудиоальбом', Yii::app()->createUrl('/community/album/audio_add', array('community_alias' => Yii::app()->community->alias)), array('id' => 'add_album', 'class' => 'fancybox.ajax btn2')); ?>
    </div>
</div>
<?php endif; ?>
<ul class="album">
    <?php foreach($models as $model): ?>
        <li>
            <div class="body">
                <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.album', array(
                    'model' => $model,
                    'route' => '/community/album/audio_show',
                    'routeParams' => array('community_alias' => $model->community_alias)
                )); ?>
            </div>
            <div class="title">
                <i class="icon" id="album"></i>
                <?php echo CHtml::link($model->title, Yii::app()->createUrl('/community/album/audio_show', array('album_id' => $model->id, 'community_alias' => $model->community_alias)))?>
                <div class="hint">В альбоме аудиозаписей: <?php echo $model->audioCount; ?></div>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
<?php if(empty($models)): ?>
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