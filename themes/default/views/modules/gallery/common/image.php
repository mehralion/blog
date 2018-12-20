<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 14:04
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryImage $model
 */
if(!isset($route))
    $route = '/gallery/image/show';
if(!isset($routeParams))
    $routeParams = array('id' => $model->id, 'gameId' => $model->user->game_id);
else
    $routeParams = CMap::mergeArray($routeParams, array('id' => $model->id));
?>
<figure class="img_border">
    <?php
        echo
            CHtml::link(
                CHtml::image($model->getImageUrl('thumbs_small'), $model->title),
                Yii::app()->createUrl($route, $routeParams)
            );
    ?>
</figure>