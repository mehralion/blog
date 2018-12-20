<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 07.06.13
 * Time: 20:01
 * To change this template use File | Settings | File Templates.
 *
 * @var EventItemVideo[] $models
 * @var CPagination $pages
 */
$this->breadcrumbs = array(
    'Подписки - Видеозаписи'
);
?>
<?php $flag = false; ?>
<?php foreach($models as $model): ?>
    <?php if(empty($model->videoAll)) continue; ?>
    <?php $flag = true; ?>
    <?php $added = '';
    if($model->albumInfo->is_community)
        $added = 'community.';
    $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.event.common.events.item.'.$added.$model->item_type.'_type', array(
        'model' => $model,
    )); ?>
<?php endforeach; ?>
<?php if(!$flag): ?>
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