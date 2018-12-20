<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 15:02
 * To change this template use File | Settings | File Templates.
 *
 * @var TbActiveForm $form
 */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'form-image-add',
    'type'=>'horizontal',
    'action' => Yii::app()->createUrl('/gallery/image/add', array('id' => Yii::app()->request->getParam('id'))),
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data',
    ),
)); ?>
    <div class="add_image_block">
        <div class="files_block">
            <ul>
                <li>
                    <input type="text" id="Files[0]">
                    <input type="file" name="Files[0]" style="display: none;">
                </li>
                <li class="m_button">
                    <button class="obzor btn1">Обзор...</button>
                    <button class="add_image_row btn1">+</button>
                </li>
            </ul>
        </div>

        <div class="buttons">
            <div class="m_button">
                <input type="submit" class="btn2" value="Загрузить выбранные фото">
            </div>
        </div>
    </div>
<?php $this->endWidget(); ?>