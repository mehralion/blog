<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 08.10.13
 * Time: 17:49
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryImage[] $models
 * @var CPagination $pages
 */

$this->breadcrumbs = array(
    'Корзина - Фотографии',
);

?>

    <ul class="image">
        <?php foreach($models as $model): ?>
            <li>
                <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.image', array(
                    'model' => $model
                )); ?>
                <div class="buttons">
                    <?php
                    echo CHtml::link(
                        '<i class="icon" id="ok" title="Восстановить"></i>',
                        Yii::app()->createUrl('/gallery/image/reset', array('id' => $model->id)),
                        array('title' => 'Восстановить')
                    );
                    echo '<span style="width:10px;display: inline-block"></span>';
                    echo CHtml::link(
                        '<i class="icon" id="no" title="Удалить навсегда"></i>',
                        Yii::app()->createUrl('/gallery/image/trunc', array('id' => $model->id)),
                        array('title' => 'Удалить навсегда', 'confirm' => "Вы уверены, что хотите удалить эту фотографию навсегда?")
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