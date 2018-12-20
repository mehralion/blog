<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 25.02.14
 * Time: 2:55
 *
 * @var Radio $model
 * @var TbActiveForm $form
 */ ?>
<style>
    #recaptcha_area input[type=text] {
        height: 26px !important;
        padding-left: 5px !important;
    }
</style>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'form-post-create',
    'type'=>'horizontal',
    'enableAjaxValidation' => false,
    'inlineErrors' => false)); ?>

<div class="" style="width: 320px;margin: 0 auto;">
    <?php echo $form->label($model, 'validation', array('style' => 'width:200px;')); ?>
    <?php $this->widget('application.extensions.recaptcha.EReCaptcha', array(
            'model' => $model,
            'attribute' => 'validation',
            'theme'=>'red',
            'language'=>'es_ES',
            'htmlOptions' => array('class' => 'captcha')
        )
    ) ?>
    <?php echo CHtml::error($model, 'validation'); ?>
</div>
    <div class="buttons" style="text-align: center">
        <div class="m_button">
            <input type="submit" class="btn2" value="Отправить">
        </div>
    </div>
<?php $this->endWidget(); ?>