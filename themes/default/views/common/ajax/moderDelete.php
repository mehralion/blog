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

<?php echo $form->textAreaRow($model, 'moder_reason', array('placeholder'=>'Причина','class'=>'span3', 'labelOptions' => array('label' => false))); ?>
<div class="buttons">
    <div class="m_button">
        <?php echo CHtml::submitButton('Удалить', array('class' => 'btn2', 'name' => 'delete')) ?>
        <?php echo CHtml::submitButton('Удалить и наказать', array('class' => 'btn1', 'name' => 'silence')) ?>
    </div>
</div>

<?php $this->endWidget(); ?>


