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
    'Отправленные приглашения'
);
?>

<?php Yii::app()->controller->renderPartial('short/invite', array(
    'pages' => $pages,
    'models' => $models
)); ?>
<div class="right">
    <ul id="inviteList" class="nolist">
        <li class="item">
            <label style="width: 219px;margin-top: 8px;margin-right: 7px;">Отправить приглашение</label>
            <div class="m_button">
                <a style="margin-top: 10px;" href="#" class="btn1 addInvite">+</a>
            </div>
        </li>
    </ul>
</div>
<div class="clear"></div>
<div class="buttons" style="text-align: center">
    <div class="m_button">
        <a id="save" href="<?php echo Yii::app()->createUrl('/community/users/update', array('community_alias' => Yii::app()->community->alias)); ?>" class="btn2">Сохранить</a>
    </div>
</div>
<script>
    var getUserListLink = '<?php echo Yii::app()->createUrl('/site/users') ?>';
</script>