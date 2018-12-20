<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnikitchenko
 * Date: 07.08.13
 * Time: 18:59
 * To change this template use File | Settings | File Templates.
 *
 * @var UserSilence $model
 */ ?>

<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'set-silence',
    'enableAjaxValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'hideErrorMessage' => true,
        'beforeValidate' => 'js:function(form) {
            return validate.beforeValidate("set-silence");
        }',
        'afterValidate' => 'js:function(form, data, hasError) {
            validate.afterValidate2(data, hasError, "set-silence");
        }'
    ),
)); ?>

<?php echo $form->textAreaRow($model, 'moder_reason', array('class'=>'span3')); ?>
<div class="buttons">
    <div class="m_button">
        <?php echo CHtml::submitButton('Наказать', array('class' => 'btn2')) ?>
    </div>
</div>

<?php $this->endWidget(); ?>


