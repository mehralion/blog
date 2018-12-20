<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 08.10.13
 * Time: 17:49
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryVideo[] $models
 * @var CPagination $pages
 */

$this->breadcrumbs = array(
    'Корзина - Видеозаписи',
);

?>

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
                        '<i class="icon" id="ok" title="Восстановить"></i>',
                        Yii::app()->createUrl('/gallery/video/reset', array('id' => $model->id)),
                        array('title' => 'Восстановить')
                    );
                    echo '<span style="width:10px;display: inline-block"></span>';
                    echo CHtml::link(
                        '<i class="icon" id="no" title="Удалить навсегда"></i>',
                        Yii::app()->createUrl('/gallery/video/trunc', array('id' => $model->id)),
                        array('title' => 'Удалить навсегда', 'confirm' => "Вы уверены, что хотите удалить эту видеозапись навсегда?")
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