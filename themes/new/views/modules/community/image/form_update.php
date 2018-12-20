<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 15:02
 * To change this template use File | Settings | File Templates.
 *
 * @var TbActiveForm $form
 * @var GalleryImage $model
 */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'form-image-update',
    'type'=>'horizontal',
    'action' => Yii::app()->createUrl('/community/image/update', array('id' => $model->id, 'community_alias' => Yii::app()->community->alias)),
    'enableAjaxValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'hideErrorMessage' => true,
        'beforeValidate' => 'js:function(form) {
            return validate.beforeValidate("form-image-update");
        }',
        'afterValidate' => 'js:function(form, data, hasError) {
            validate.afterValidate2(data, hasError, "form-image-update");
        }'
    ),
)); ?>
<div class="updateList">
    <div class="img_border">
        <?php echo CHtml::image($model->getImageUrl()) ?>
    </div>
    <ul class="about_photo">
        <li class="name">
            <?php echo $form->textFieldRow($model, 'title', array(
                'class'=>'span3',
                'labelOptions' => array('label' => false),
                'maxlength' => 70
            )); ?>
        </li>
        <li>
            <?php echo $form->dropDownListRow($model, 'album_id', GalleryAlbumImage::getCommunityAlbums($model->community_id)); ?>
        </li>
        <li class="isComment">
            <?php echo $form->checkBoxRow($model, 'is_comment', array('labelOptions' => array('label' => false))); ?>
        </li>
        <li>
            <?php $this->widget('application.widgets.editor.EditorWidget', array(
                    'model' => $model,
                    'attributeName' => 'description',
                    'htmlOptions' => array('class' => 'span8'),
                    'form' => $form,
                    'button' => false
                )
            );  ?>
        </li>
    </ul>
    <div class="buttons" style="text-align: center">
        <div class="m_button">
            <input type="submit" class="btn1" value="Сохранить">
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>