<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 14.06.13
 * Time: 19:15
 * To change this template use File | Settings | File Templates.
 *
 * @var User[] $models
 * @var CPagination $pages
 */ ?>
    <ul class="top">
        <?php if($this->beginCache('user_top_list_'.$_GET['page'], array('dependency' => $dependency))) { ?>
            <?php foreach($models as $model): ?>
                <li>
                    <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.user.common.short.user',
                        array(
                            'model' => $model
                        )); ?>
                </li>
            <?php endforeach; ?>
            <?php $this->endCache(); } ?>
    </ul>

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