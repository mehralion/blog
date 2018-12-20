<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 16:29
 * To change this template use File | Settings | File Templates.
 *
 * @var TbActiveForm $form
 * @var GalleryImage[] $models
 */
?>
<div class="updateList">
    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'form-image-add',
        'type' => 'horizontal',
        'action' => Yii::app()->createUrl('/gallery/image/updatelist'),
    )); ?>
    <fieldset>
        <?php foreach ($models as $key => $model): ?>
            <div class="item">
                <?php echo CHtml::hiddenField('GalleryImage['.$key.'][id]', $model->id); ?>
                <div class="img_border">
                    <?php echo CHtml::link(
                        CHtml::image($model->getImageUrl('thumbs_small')),
                        $model->getImageUrl('thumbs_big'),
                        array(
                            'class' => 'preview_img fancybox'
                        )
                    ); ?>
                </div>
                <ul class="about_photo">
                    <li class="name">
                        <?php echo CHtml::textField('GalleryImage['.$key.'][title]', $model->title, array('placeholder' => 'Введите название фотографии', 'maxlength' => 70)) ?>
                    </li>
                    <li class="isComment">
                        <?php echo CHtml::checkBox('GalleryImage['.$key.'][is_comment]', $model->is_comment) ?>
                        <?php echo CHtml::label($model->getAttributeLabel('is_comment'), '') ?>
                    </li>
                    <li>
                        <?php $this->widget('application.widgets.editor.EditorWidget', array(
                                'model' => $model,
                                'attributeName' => 'description',
                                'htmlOptions' => array(
                                    'name' => 'GalleryImage['.$key.'][description]',
                                    'placeholder' => 'Введите описание фотографии...'
                                ),
                                'form' => $form,
                                'button' => false,
                            )
                        );  ?>
                    </li>
                </ul>
            </div>
            <hr>
        <?php endforeach; ?>
    </fieldset>
    <div class="buttons" style="text-align: center;">
        <div class="m_button">
            <input type="submit" class="btn2" value="Сохранить">
        </div>
    </div>

    <?php $this->endWidget(); ?>
</div>