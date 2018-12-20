<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 02.01.14
 * Time: 10:00
 *
 * @var TbActiveForm $form
 * @var Community $model
 */
$this->breadcrumbs = array(
    'Мои сообщества' => Yii::app()->createUrl('/community/profile/own', array('gameId' => Yii::app()->user->getGameId())),
    'Сообщество'
);
?>
<div class="form shadow community">
    <section id="headerInfo">
        <div class="content">Создание сообщества: Шаг первый</div>
    </section>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>'form-community-create',
        'type'=>'horizontal',
        'enableAjaxValidation' => true,
        'inlineErrors' => false,
        'clientOptions' => array(
            'errorCssClass' => '',
            'successCssClass' => '',
            'validateOnSubmit' => true,
            'validateOnChange' => false,
            'beforeValidate' => 'js:function(form) {
                return validate.beforeValidate("form-community-create");
            }',
            'afterValidate' => 'js:function(form, data, hasError) {
                validate.afterValidate2(data, hasError, "form-community-create");
                if(data.content !== undefined)
                    $(".form.community").replaceWith(data.content);
            }',
        ),
    )); ?>
    <div class="left">
        <?php echo $form->textFieldRow($model, 'title', array('class'=>'span3', 'labelOptions' => array('class' => 'short'), 'maxlength' => 70)); ?>
        <?php echo $form->textFieldRow($model, 'alias', array('class'=>'span3', 'labelOptions' => array('class' => 'short'), 'maxlength' => 70)); ?>
    </div>
    <div class="right">
        <?php echo $form->dropDownListRow(
            $model,
            'category_id',
            GxHtml::listDataEx(CommunityCategory::model()->findAllAttributes(null, true)),
            array('class'=>'span3', 'labelOptions' => array('class' => 'short'), 'maxlength' => 70)); ?>
        <?php echo $form->dropDownListRow($model, 'view_role', Community::getTypes(), array('class'=>'span3', 'labelOptions' => array('class' => 'short'), 'maxlength' => 70)); ?>
    </div>
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
                <a href="<?php echo Yii::app()->createUrl('/community/profile/own', array('gameId' => Yii::app()->user->getGameId())); ?>" class="btn1">Отмена</a>
                <input type="submit" class="btn2" value="Дальше">
            </div>
        </div>

    <?php $this->endWidget(); ?>
</div>