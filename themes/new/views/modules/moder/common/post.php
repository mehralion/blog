<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 11.07.13
 * Time: 14:57
 * To change this template use File | Settings | File Templates.
 *
 * @var ModerLog $model
 * @var Post $Post
 */
echo 'Модератор '.$model->moder->getFullLogin().' ';
if($model->operation_type == ModerLog::ITEM_OPERATION_DELETE)
    echo 'удалил заметку ('.CHtml::link(
            'перейти',
            Yii::app()->createUrl('/preview/post', array('id' => $model->post->id)),
            array('class' => 'fancybox.ajax view')
        ).')';
elseif($model->operation_type == ModerLog::ITEM_OPERATION_RESTORE)
    echo 'восстановил заметку ('.CHtml::link(
            'перейти',
            Yii::app()->createUrl('/preview/post', array('id' => $model->post->id)),
            array('class' => 'fancybox.ajax view')
        ).')';
else
    echo 'отклонил жалобу по заметке ('.CHtml::link(
            'перейти',
            Yii::app()->createUrl('/preview/post', array('id' => $model->post->id)),
            array('class' => 'fancybox.ajax view')
        ).')';