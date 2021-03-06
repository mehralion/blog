<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 02.01.14
 * Time: 9:40
 *
 * @var Community[] $models
 */

$this->breadcrumbs = array(
    'Мои сообщества'
);
?>
<div class="buttons">
    <i class="icon" id="post_icon"></i>
    <div class="m_button">
        <?php echo CHtml::link(
            'Добавить сообщество',
            Yii::app()->createUrl('/community/profile/create', array('gameId' => Yii::app()->user->getGameId())),
            array('id' => 'add_community', 'class' => 'fancybox.ajax btn2')); ?>
    </div>
</div>

<ul class="top community" style="margin-top: 10px;">
        <?php foreach($models as $model): ?>
    <li>
            <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.community.common.short', array(
                'model' => $model
            )); ?>
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