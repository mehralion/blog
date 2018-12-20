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
 * @var Poll $poll
 */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'form-post-create',
    'type'=>'horizontal',
    'action' => $url,
    'enableAjaxValidation' => true,
    'inlineErrors' => false,
    'clientOptions' => array(
        'errorCssClass' => '',
        'successCssClass' => '',
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'beforeValidate' => 'js:function(form) {
            return validate.beforeValidate("form-post-create");
        }',
        'afterValidate' => 'js:function(form, data, hasError) {
            validate.afterValidate2(data, hasError, "form-post-create");
            $("#Poll_date_end").attr("readOnly", true);
        }',
    ),
)); ?>
<?php echo $form->textFieldRow($model, 'title', array('class'=>'span3', 'labelOptions' => array('class' => 'short'), 'maxlength' => 70)); ?>
<?php $this->widget('application.widgets.editor.EditorWidget', array(
        'model' => $model,
        'attributeName' => 'description',
        'htmlOptions' => array('class' => 'short'),
        'form' => $form,
        'button' => false
    )
);  ?>
<?php echo $form->checkBoxRow($model, 'is_comment', array('labelOptions' => array('class' => 'short'))); ?>
<?php echo $form->dropDownListRow(
    $model,
    'view_role',
    Yii::app()->access->getRoleViewList(true),
    array(
        'labelOptions' => array('class' => 'short'),
        'hint'=>'В ТОП рейтингах участвуют только те заметки, которые доступны всем'
    )); ?>
<?php if($model->isNewRecord): ?>
    <?php echo $form->checkBoxRow($model, 'is_poll', array('labelOptions' => array('class' => 'short'))); ?>
    <div class="poll hidden">
        <div class="" style="text-align: center;margin-bottom: 10px;font-style: italic;">
            После создания заметки вы не сможете отредактировать опрос.
        </div>
        <div class="control-group" style="display: inline-block;">
            <label class="short control-label">Вопрос <span class="required">*</span></label>
            <div class="controls">
                <?php echo $form->textField($poll, 'question'); ?>
                <?php echo $form->error($poll, 'question', array('style' => 'display: block;')); ?>
            </div>
        </div>
        <div class="control-group" style="display: inline-block;vertical-align: top;margin-top: -2px;">
            <div class="controls">
                <?php echo $form->textField($poll, 'date_end', array('placeholder' => 'Дата завершения', 'readOnly' => true)); ?>
                <?php echo $form->error($poll, 'date_end'); ?>
            </div>
        </div>
        <div class="control-group blockPoll">
            <label class="short control-label">Вариант 1 <span class="required">*</span></label>
            <div class="controls" style="display: inline-block;margin-left: 15px;">
                <?php echo $form->textField($poll, 'answer', array('name' => 'Poll[answer][0]', 'id' => 'Poll_0_answer')); ?>
                <?php echo $form->error($poll, 'answer', array('id' => 'Poll_0_answer_em_')); ?>
            </div>
            <div class="m_button" style="vertical-align:top;">
                <?php echo CHtml::link('+', '', array('class' => 'btn1 add')); ?>
            </div>
        </div>
        <div id="pollAnswer">

        </div>
    </div>
<?php endif; ?>
    <div class="buttons" style="text-align: center">
        <div class="m_button">
            <input type="submit" class="btn2" value="Сохранить">
        </div>
    </div>

<?php $this->endWidget(); ?>
<script>
    $(function(){
        $.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );
        $('#Poll_date_end').datepicker();
    });
</script>