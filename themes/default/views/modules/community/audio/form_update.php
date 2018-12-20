<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 15:02
 * To change this template use File | Settings | File Templates.
 *
 * @var TbActiveForm $form
 * @var GalleryAudio $model
 */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'form-image-update',
    'type'=>'horizontal',
    'action' => Yii::app()->createUrl('/community/audio/update', array('id' => $model->id, 'community_alias' => Yii::app()->community->alias)),
    'enableAjaxValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'hideErrorMessage' => true,
        'beforeValidate' => 'js:function(form) {
            return validate.beforeValidate("form-audio-update");
        }',
        'afterValidate' => 'js:function(form, data, hasError) {
            validate.afterValidate2(data, hasError, "form-audio-update");
        }'
    ),
)); ?>
    <div class="updateList">
        <ul class="about_photo">
            <li>
                <?php echo $form->dropDownListRow($model, 'album_id', GalleryAlbumAudio::getCommunityAlbums(Yii::app()->community->id)); ?>
            </li>
            <li class="name">
                <?php echo $form->textFieldRow($model, 'title', array(
                    'class'=>'span3',
                    'labelOptions' => array('label' => false),
                    'maxlength' => 100
                )); ?>
            </li>
        </ul>
        <div class="buttons" style="text-align: center">
            <div class="m_button">
                <input type="submit" class="btn1" value="Сохранить">
            </div>
        </div>
    </div>
<?php $this->endWidget(); ?>