<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 12.06.13
 * Time: 12:48
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryVideo[] $models
 * @var CPagination $pages
 */
$this->breadcrumbs = array(
    'Видео',
);

?>
<div class="buttons">
    <i class="icon" id="video_icon"></i>
    <div class="m_button">
        <?php echo CHtml::link('Добавить видео', Yii::app()->createUrl('/gallery/video/add'), array('id' => 'add_video', 'class' => 'fancybox.ajax btn2')); ?>
    </div>
</div>
<ul class="video">
    <?php foreach($models as $model): ?>
        <li>
            <div class="">
                <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.video', array(
                    'model' => $model,
                )); ?>
            </div>
            <div class="buttons">
                <?php
                echo CHtml::link(
                    '<i class="icon" id="edit" title="Редактировать"></i>',
                    Yii::app()->createUrl('/gallery/video/update', array('id' => $model->id)),
                    array('class' => 'edit fancybox.ajax', 'title' => 'Редактировать')
                );
                echo '<span style="width:10px;display: inline-block"></span>';
                echo CHtml::link(
                    '<i class="icon" id="del" title="Удалить"></i>',
                    Yii::app()->createUrl('/gallery/video/delete', array('id' => $model->id)),
                    array('title' => 'Удалить', 'confirm' => 'Вы уверены, что хотите удалить это видео?')
                );
                ?>
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