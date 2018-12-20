<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 13.11.13
 * Time: 17:56
 *
 * @var TbActiveForm $form
 * @var UserSubscribe $model
 */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'form-subscribe',
    'type'=>'horizontal',
    'enableAjaxValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'hideErrorMessage' => true,
        'beforeValidate' => 'js:function(form) {
            return validate.beforeValidate("form-subscribe");
        }',
        'afterValidate' => 'js:function(form, data, hasError) {
            validate.afterValidate2(data, hasError, "form-subscribe");
        }'
    ),
    'htmlOptions' => array(
        'style' => 'width:510px;margin-bottom:0px;'
    )
)); ?>
<div class="">
    <label class="checkbox"><input type="checkbox" id="checkAll"> Все</label>
</div>
<?php echo $form->checkBoxRow($model, 'post', array('class' => 'checkSubscribe', 'labelOptions' => array('class' => 'short'))); ?>
<?php echo $form->checkBoxRow($model, 'image', array('class' => 'checkSubscribe', 'labelOptions' => array('class' => 'short'))); ?>
<?php echo $form->checkBoxRow($model, 'audio', array('class' => 'checkSubscribe', 'labelOptions' => array('class' => 'short'))); ?>
<?php echo $form->checkBoxRow($model, 'video', array('class' => 'checkSubscribe', 'labelOptions' => array('class' => 'short'))); ?>
<?php echo $form->checkBoxRow($model, 'comment', array('class' => 'checkSubscribe', 'labelOptions' => array('class' => 'short'))); ?>
    <div class="buttons" style="text-align: center;margin-top: 0px;">
        <div class="m_button">
            <input type="submit" class="btn2" value="Сохранить">
        </div>
    </div>

<?php $this->endWidget(); ?>
<script>
    $(function(){
        $(document.body).on('click', '#checkAll', function(){
            var $self = $(this);
            if($self.is(':checked'))
                $('#form-subscribe .checkSubscribe').attr('checked', true);
            else
                $('#form-subscribe .checkSubscribe').attr('checked', false);
        });
    });
</script>