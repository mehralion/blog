<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 22.06.13
 * Time: 9:22
 * To change this template use File | Settings | File Templates.
 */
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'send-comment-form',
    'type'=>'horizontal',
    'action' => $this->url,
    'enableAjaxValidation' => false,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'hideErrorMessage' => true,
        'beforeValidate' => 'js:function(form) {
            return validate.beforeValidate("send-comment-form");
        }',
        'afterValidate' => 'js:function(form, data, hasError) {
            validate.afterValidate(data, hasError, "send-comment-form");
        }',
    ),
    'htmlOptions' => array(
        'style' => 'text-align:center;'
    ),
));
?>
<ul class="text_editor">
    <li class="editor">
        <textarea name="<?php echo get_class($this->model).'['.$this->attributeName.']'; ?>" id="editor"></textarea>
    </li>
    <li>
        <ul class="buttons">
            <li id="b"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/b.png'); ?></li>
            <li id="i"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/i.png'); ?></li>
            <li id="u"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/u.png'); ?></li>
            <li id="color"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/color.png'); ?></li>
            <li id="hide"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/hide.png'); ?></li>
            <li id="info"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/info.png'); ?></li>
            <li id="link"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/link.png'); ?></li>
            <li id="image"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/pic.png'); ?></li>
            <li id="smile"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/smile.png'); ?></li>
        </ul>
    </li>
    <li class="buttons" style="text-align: center;margin-top: 20px;margin-bottom: 20px;">
        <div class="m_button">
            <input type="submit" class="btn2" value="Добавить">
        </div>
    </li>
</ul>
<?php $this->endWidget(); ?>