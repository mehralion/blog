<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 02.01.14
 * Time: 9:40
 *
 * @var Community[] $models
 * @var CPagination $pages
 * @var CommunityCategory $category
 */
$this->breadcrumbs = array(
    'Сообщества' => Yii::app()->createUrl('/community/request/index'),
    $category->title
);
?>

<ul class="top">
    <?php if($this->beginCache('all_community_'.$category->id, array('dependency' => Community::getTbDependency()))): ?>
        <?php foreach($models as $model): ?>
            <li>
                <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.community.common.short_top',
                    array('model' => $model)); ?>
            </li>
        <?php endforeach; ?>
    <?php $this->endCache(); endif; ?>
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