<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 14.06.13
 * Time: 19:15
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryImage[] $models
 * @var CPagination $pages
 */ ?>
<ul class="top">
    <?php foreach($models as $model): ?>
        <li>
            <?php $added = '';
            if($model->is_community)
                $added = 'community.';
            $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.short.'.$added.'audio',
                array(
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