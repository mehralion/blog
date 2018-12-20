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

$this->widget('xupload.XUpload', array(
    'url' => Yii::app()->createUrl("/gallery/image/upload", array('id' => $album_id)),
    'model' => $model,
    'attribute' => 'file_name',
    'multiple' => true,
    'options' => array(
        'sequentialUploads' => true,
        'maxFileSize' => 3000000,
        'limitMultiFileUploads' => 1,
        'acceptFileTypes' => "js:/(\.|\/)(jpe?g|png|gif)$/i",
        'completed' => 'js:function (event, files, index, xhr, handler, callBack) {
                    $.each(files.result, function(i, data){addItems(data);});
                  }',
    )
));
 ?>