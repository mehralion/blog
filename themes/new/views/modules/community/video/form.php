<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 14:15
 * To change this template use File | Settings | File Templates.
 *
 * @var TbActiveForm $form
 * @var GalleryVideo $model
 */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'video-form',
    'type'=>'horizontal',
    'enableAjaxValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'hideErrorMessage' => true,
        'beforeValidate' => 'js:function(form) {
            return validate.beforeValidate("horizontalForm");
        }',
        'afterValidate' => 'js:function(form, data, hasError) {
            validate.afterValidate2(data, hasError, "horizontalForm");
        }'
    ),
)); ?>

        <?php echo $form->textFieldRow($model, 'title', array('labelOptions' => array('class' => 'short'), 'maxlength' => 70)); ?>
        <?php $this->widget('application.widgets.editor.EditorWidget', array(
                'model' => $model,
                'attributeName' => 'description',
                'htmlOptions' => array('class' => 'span8'),
                'form' => $form,
                'button' => false
            )
        );  ?>

        <?php if($update) echo $form->dropDownListRow($model, 'album_id', GalleryAlbumVideo::getCommunityAlbums($model->community_id), array('labelOptions' => array('class' => 'short'))); ?>
        <?php echo $form->dropDownListRow($model, 'video_type', $model->getVideoTypes(), array(
            'labelOptions' => array('class' => 'short'),
            'disabled' => !$canEdit
        )); ?>
        <?php echo $form->textFieldRow($model, 'link', array('labelOptions' => array('class' => 'short'), 'disabled' => !$canEdit)); ?>
        <?php echo $form->checkBoxRow($model, 'is_comment'); ?>
    <div class="buttons" style="text-align: center">
        <div class="m_button">
            <input type="submit" value="Сохранить" class="btn2">
        </div>
    </div>

<?php $this->endWidget(); ?>