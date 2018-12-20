<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 19:51
 * To change this template use File | Settings | File Templates.
 *
 * @var TbActiveForm $form
 * @var Post $model
 */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'form-bug',
    'type'=>'horizontal',
    'action' => Yii::app()->createUrl('/site/bug'),
    'enableAjaxValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'hideErrorMessage' => true,
        'beforeValidate' => 'js:function(form) {
            return validate.beforeValidate("form-bug");
        }',
        'afterValidate' => 'js:function(form, data, hasError) {
            validate.afterValidate2(data, hasError, "form-bug");
        }'
    ),
    'htmlOptions' => array('style' => 'width:600px')
)); ?>
<div class="event_empty">Пожалуйста, как можно детальнее опишите проблему и в процессе чего она возникла, можно со скриншотами.</div>
<br>
<?php $form->textFieldRow($model, 'user_id'); ?>
<?php $this->widget('application.widgets.editor.EditorWidget', array(
        'model' => $model,
        'attributeName' => 'description',
        'htmlOptions' => array('class' => 'short'),
        'form' => $form,
        'button' => false
    )
);  ?>
    <div class="buttons" style="text-align: center">
        <div class="m_button">
            <input type="submit" class="btn2" value="Отправить">
        </div>
    </div>

<?php $this->endWidget(); ?>