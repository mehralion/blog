<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 28.07.13
 * Time: 15:27
 *
 * @var User $model
 */ ?>
<div class="sidebar" style="background-color: #d6d2b9;padding: 3px;border: 1px solid #c1bead;">
    <div class="" style="background-color: #f0ecd6;">
        <h2 class="title">МЕНЮ</h2>
        <?php /** @var TbActiveForm $form */
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'=>'login-form',
            'htmlOptions' => array(
                'style' => 'border-top:1px solid #c1bead;padding-bottom:4px;margin: 7px 7px 0;overflow: hidden;'
            )
        )); ?>

        <?php echo $form->textFieldRow($model, 'login', array('placeholder' => 'Логин', 'class'=>'span3', 'labelOptions' => array('label' => false))); ?>
        <input class="span3 pwd_custom" name="User[psw]" type="text" value=""/>
        <div class="buttons">
            <div class="m_button">
                <input type="submit" class="btn1" value="Войти" style="margin-top: -2px;">
                <a href="https://oldbk.com/reg.php" target="_blank" class="btn2">Регистрация</a>
            </div>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>