<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 14:04
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryAlbum $model
 */
if(!isset($routeParams)) $routeParams = array();

echo CHtml::link(
    $model->getImage(),
    Yii::app()->createUrl($route, CMap::mergeArray(array('album_id' => $model->id), $routeParams)),
    array('rel' => $model->id)
);