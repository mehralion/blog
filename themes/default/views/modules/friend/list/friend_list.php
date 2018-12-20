<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 14:57
 * To change this template use File | Settings | File Templates.
 *
 * @var UserFriend[] $models
 * @var CPagination $pages
 */
$this->breadcrumbs = array(
    'Друзья',
);
?>
<?php echo $menu; ?>
    <ul class="friend">
        <?php foreach($models as $model): ?>
            <li>
                <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.friend.common.friend', array(
                    'model' => $model,
                    'type' => $type
                )); ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="event_empty">
        <?php if(empty($models)): ?>
            <?php if($type == 'friend'): ?>
                К сожалению, у Вас пока нет друзей.
                Для того, чтобы добавить к себе друга, зайдите на его страницу блогов и нажмите слева "Добавить в друзья".
            <?php else: ?>
                Список пуст
            <?php endif; ?>
        <?php else: ?>
            <?php if($type == 'pending'): ?>
                Эти люди хотят стать вашими друзьями. Вы можете подтвердить дружбу или отказаться.
            <?php elseif($type == 'own'): ?>
                Вы послали запросы на добавление в друзья этим людям, но они пока вам не ответили.
            <?php endif; ?>
        <?php endif; ?>
    </div>
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