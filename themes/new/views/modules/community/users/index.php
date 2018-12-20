<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 04.01.14
 * Time: 8:50
 *
 * @var CommunityUser[] $models
 * @var CPagination $pages
 */
$this->breadcrumbs = array(
    'Сообщество - '.Yii::app()->community->title => Yii::app()->createUrl('/community/request/show', array('community_alias' => Yii::app()->community->alias)),
    'Участники'
);
?>

<?php Yii::app()->controller->renderPartial('short/users', array(
    'pages' => $pages,
    'models' => $models
)); ?>
<div class="right">

</div>
<div class="clear"></div>