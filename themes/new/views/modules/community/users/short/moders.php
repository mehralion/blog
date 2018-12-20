<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 04.01.14
 * Time: 9:15
 *
 * @var CPagination $pages
 * @var CommunityUser[] $models
 */ ?>

<div id="userList" class="left">
    <input type="hidden" id="page" value="<?php echo $pages->currentPage + 1; ?>">
    <ul>
        <?php foreach($models as $model ): ?>
            <li>
                <?php if(Yii::app()->community->user_id == Yii::app()->user->id): ?>
                    <?php echo CHtml::link(
                        '<i class="icon" id="del" title="Удалить"></i>',
                        Yii::app()->createUrl(
                            '/community/users/delete_moder',
                            array(
                                'community_alias' => Yii::app()->community->alias,
                                'user_id' => $model->user_id,
                            )
                        ),
                        array('class' => 'delete')
                    ); ?>
                <?php endif; ?>
                <?php echo $model->user->getFullLogin(); ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php $this->widget('ext.pagination.Pager', array(
        //'cssFile' => '',
        'internalPageCssClass' => 'btn',
        'pages' => $pages,
        'header' => '',
        'selectedPageCssClass' => 'active',
        'htmlOptions' => array(
            'class' => 'btn-group pagination',
        )
    )); ?>
</div>