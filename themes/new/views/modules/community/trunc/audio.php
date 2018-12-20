<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 08.10.13
 * Time: 17:49
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryAudio[] $models
 */

$this->breadcrumbs = array(
    Yii::app()->community->title => Yii::app()->createUrl('/community/request/show', array('community_alias' => Yii::app()->community->alias)),
    'Корзина - Аудиозаписи',
);
?>
<style>
    ul.audio li {
        margin-right: 30px;
    }
</style>
    <ul class="audio">
        <?php foreach($models as $model): ?>
            <li>
                <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.audio', array(
                    'model' => $model
                )); ?>
                <?php echo ' '.$model->title; ?>
                    <div class="buttons">
                        <?php
                        echo CHtml::link(
                            '<i class="icon" id="ok" title="Восстановить"></i>',
                            Yii::app()->createUrl('/community/audio/reset', array('id' => $model->id, 'community_alias' => $model->community_alias)),
                            array('title' => 'Восстановить')
                        );
                        echo '<span style="width:10px;display: inline-block"></span>';
                        echo CHtml::link(
                            '<i class="icon" id="no" title="Удалить навсегда"></i>',
                            Yii::app()->createUrl('/community/audio/trunc', array('id' => $model->id, 'community_alias' => $model->community_alias)),
                            array('title' => 'Удалить навсегда', 'confirm' => "Вы уверены, что хотите удалить эту аудиозапись навсегда?")
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
    //'cssFile' => '',
    'internalPageCssClass' => 'btn',
    'pages' => $pages,
    'header' => '',
    'selectedPageCssClass' => 'active',
    'htmlOptions' => array(
        'class' => 'btn-group pagination',
    )
)); ?>