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
    'action' => Yii::app()->createUrl('/community/audio/add', array('album_id' => $model->id, 'community_alias' => Yii::app()->community->alias)),
)); ?>
    <div class="add_image_block">
        <div class="files_block">
            <ul>
                <li>
                    <input type="text" name="Files[0][link]" placeholder="Ссылка на файл (только mp3)">
                </li>
                <li>
                    <input type="text" name="Files[0][title]" maxlength="70" placeholder="Название">
                </li>
                <li class="m_button">
                    <button class="add_row btn1">+</button>
                </li>
            </ul>
        </div>

        <div class="buttons">
            <div class="m_button">
                <input type="submit" class="btn2" value="Добавить">
            </div>
        </div>
    </div>
<?php $this->endWidget(); ?>