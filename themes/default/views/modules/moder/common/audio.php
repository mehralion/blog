<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 11.07.13
 * Time: 14:56
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryImage $Image
 * @var ModerLog $model
 */
echo 'Модератор '.$model->moder->getFullLogin().' ';
if($model->operation_type == ModerLog::ITEM_OPERATION_DELETE)
    echo 'удалил аудиоальбом ('.
        CHtml::link(
            'перейти',
            Yii::app()->createUrl('/preview/audio', array('id' => $model->audio->id)),
            array('class' => 'fancybox.ajax view')
        ).')';
elseif($model->operation_type == ModerLog::ITEM_OPERATION_RESTORE)
    echo 'восстановил аудиоальбом ('.CHtml::link(
            'перейти',
            Yii::app()->createUrl('/preview/audio', array('id' => $model->audio->id)),
            array('class' => 'fancybox.ajax view')
        ).')';
else
    echo 'отклонил жалобу по аудиоальбому ('.CHtml::link(
    'перейти',
    Yii::app()->createUrl('/preview/audio', array('id' => $model->audio->id)),
    array('class' => 'fancybox.ajax view')
).')';
