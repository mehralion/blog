<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 02.01.14
 * Time: 10:00
 *
 * @var TbActiveForm $form
 * @var Community $model
 */ ?>
<div class="form shadow community">
    <section id="headerInfo">
        <div class="content">Создание сообщества: Шаг второй</div>
    </section>
    <div class="left">
        <ul id="moderLsit" class="nolist">
            <li class="item">
                <label style="width: 219px;margin-top: 8px;margin-right: 7px;">Добавить модератора</label>
                <div class="m_button">
                    <a style="margin-top: 10px;" href="#" class="btn1 addModer">+</a>
                </div>
            </li>
        </ul>
    </div>
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
    <div class="buttons" style="text-align: center;">
        <div class="m_button">
            <a style="margin-top: 10px;" href="<?php echo Yii::app()->createUrl('/community/profile/create', array('community_alias' => $model->alias)); ?>" class="btn1 back">Вернуться</a>
            <a style="margin-top: 10px;" id="save" href="<?php echo Yii::app()->createUrl('/community/users/update', array('community_alias' => $model->alias)); ?>" class="btn2">Сохранить</a>
        </div>
    </div>
</div>

<script>
    var getUserListLink = '<?php echo Yii::app()->createUrl('/site/users') ?>';
</script>