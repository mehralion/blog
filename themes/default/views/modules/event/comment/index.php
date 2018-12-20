<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 07.06.13
 * Time: 18:29
 * To change this template use File | Settings | File Templates.
 *
 * @uses CPagination $pages
 * @uses EventComment[] $models
 * @var CPagination $pages
 * @var EventComment[] $models
 */
$this->breadcrumbs = array(
    'Обновления' => false,
    'Комментарии' => false,
);
?>
<?php foreach($models as $model): ?>
    <?php $added = '';
    if($model->info->is_community)
        $added = 'community.';
    $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.event.common.events.comment.'.$added.$model->item_type.'_type', array(
        'model' => $model
    )); ?>
<?php endforeach; ?>
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