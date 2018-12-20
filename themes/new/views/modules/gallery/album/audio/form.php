<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 14:15
 * To change this template use File | Settings | File Templates.
 *
 * @var TbActiveForm $form
 * @var GalleryAlbumAudio $model
 */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'add-album-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data',
    ),
)); ?>

<?php echo $form->textFieldRow($model, 'title', array('maxlength' => 70)); ?>
<label>Обложка</label>
<input type="text" class="field" style="margin-right: 10px;" readonly="true">
<div class="m_button"><span class="btn1 obzor">Обзор...</span></div>
<?php echo $form->fileFieldRow($model, 'image_front', array('maxlength' => 70, 'style' => 'position:absolute;left: -1000px;top:-1000px;', 'labelOptions' => array('label' => false))); ?>
<?php echo $form->checkBoxRow($model, 'is_comment'); ?>
<?php echo $form->dropDownListRow(
    $model,
    'view_role',
    Yii::app()->access->getRoleViewList(),
    array(
        'labelOptions' => array('class' => 'short'),
        'hint' => 'В ТОП рейтингах участвуют только те аудиозаписи, которые доступны всем'
    )); ?>
<div class="buttons" style="text-align: center">
    <div class="m_button">
        <input type="submit" class="btn2" value="Сохранить">
    </div>
</div>

<?php $this->endWidget(); ?>
<script>
    $(function () {
        $(document.body).on('change', '#add-album-form #GalleryAlbumAudio_image_front', function () {
            if ($(this).val() != '')
                $('#add-album-form .field').val($(this).val());
        });
        $(document.body).on('click', '#add-album-form .obzor', function () {
            $('#GalleryAlbumAudio_image_front').trigger('click');
        });
    });
</script>