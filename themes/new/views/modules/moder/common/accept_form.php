<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 04.07.13
 * Time: 12:50
 * To change this template use File | Settings | File Templates.
 */ ?>

    <style>
        .controls {
            margin-left: 115px !important;
        }
    </style>
<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'moder-report-form',
    'type'=>'horizontal',
    'enableAjaxValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'hideErrorMessage' => true,
        'beforeValidate' => 'js:function(form) {
            return validate.beforeValidate("moder-report-form");
        }',
        'afterValidate' => 'js:function(form, data, hasError) {
            validate.afterValidate2(data, hasError, "moder-report-form");
        }'
    ),
)); ?>
<?php echo $form->textAreaRow($model, 'moder_reason', array('class'=>'span5', 'rows'=>4, 'labelOptions' => array('class' => 'short'))); ?>
    <div class="buttons" style="padding-left: 6px;text-align: center;">
        <div class="m_button">
            <?php echo CHtml::submitButton(
                'Удалить',
                array(
                    'class' => 'btn2 submit',
                    'name' => 'Report[delete]'
                )
            ); ?>
        </div>
        <div class="m_button">
            <?php echo CHtml::submitButton(
                'Удалить и наказать',
                array(
                    'class' => 'btn1 submit',
                )
            ); ?>
        </div>
    </div>
<?php $this->endWidget(); ?>