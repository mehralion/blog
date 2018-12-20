<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 08.10.13
 * Time: 17:49
 * To change this template use File | Settings | File Templates.
 *
 * @var Post[] $models
 * @var CPagination $pages
 */

$this->breadcrumbs = array(
    'Корзина - Заметки',
);

foreach($models as $model): ?>
    <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.post.common.post_out_view', array(
        'model' => $model
    )); ?>
<?php endforeach; ?>
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